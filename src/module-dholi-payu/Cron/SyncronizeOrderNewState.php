<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.3
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Cron;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class SyncronizeOrderNewState {

	private $orderRepository;

	private $searchCriteriaBuilder;

	private $connectionPool;

	private $logger;

	public function __construct(LoggerInterface $logger,
	                            OrderRepository $orderRepository,
	                            SearchCriteriaBuilder $searchCriteriaBuilder,
	                            ResourceConnection $connectionPool = null) {
		$this->logger = $logger;
		$this->orderRepository = $orderRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->connectionPool = $connectionPool ?: ObjectManager::getInstance()->get(ResourceConnection::class);
	}

	public function execute() {
		/**
		 * Payment Methods
		 */
		$filterCc = new Filter();
		$filterCc->setField('method')->setValue(\Dholi\PayU\Model\Ui\Cc\ConfigProvider::CODE)->setConditionType('eq');

		$filterBoleto = new Filter();
		$filterBoleto->setField('method')->setValue(\Dholi\PayU\Model\Ui\Boleto\ConfigProvider::CODE)->setConditionType('eq');

		$filterBaloto = new Filter();
		$filterBaloto->setField('method')->setValue(\Dholi\PayU\Model\Ui\Baloto\ConfigProvider::CODE)->setConditionType('eq');

		$filterEfecty = new Filter();
		$filterEfecty->setField('method')->setValue(\Dholi\PayU\Model\Ui\Efecty\ConfigProvider::CODE)->setConditionType('eq');

		$filterPaymentMethodGroup = new FilterGroup();
		$filterPaymentMethodGroup->setFilters([$filterCc, $filterBoleto, $filterBaloto, $filterEfecty]);

		/**
		 * Payment State
		 */
		$filterPaymentStatus = new Filter();
		$filterPaymentStatus->setField('payu_transaction_state')->setValue(\Dholi\PayU\Gateway\PayU\Enumeration\PayUTransactionState::APPROVED()->key())->setConditionType('eq');

		$filterPaymentStatusGroup = new FilterGroup();
		$filterPaymentStatusGroup->setFilters([$filterPaymentStatus]);

		/**
		 * Order State
		 */
		$filterOrderState = new Filter();
		$filterOrderState->setField('state')->setValue(Order::STATE_NEW)->setConditionType('eq');

		$filterOrderGroup = new FilterGroup();
		$filterOrderGroup->setFilters([$filterOrderState]);

		/**
		 * Criteria
		 */
		$searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$filterOrderGroup, $filterPaymentStatusGroup, $filterPaymentMethodGroup])->create();
		$collection = $this->orderRepository->getList($searchCriteria);

		//$this->logger->info(sprintf("%s SQL %s", __METHOD__, $collection->getSelect()));
		$orderList = $collection->getItems();

		if (count($orderList)) {
			$processor = ObjectManager::getInstance()->get(\Dholi\PayU\Model\PaymentManagement\Processor::class);
			$salesConnection = $this->connectionPool->getConnection('sales');

			foreach ($orderList as $order) {
				try {
					$salesConnection->beginTransaction();
					$this->logger->info(sprintf("%s - Synchronizing Order %s", __METHOD__, $order->getIncrementId()));
					$processor->syncronize($order->getPayment(), false, $order->getGrandTotal());
				} catch (\Exception $e) {
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getMessage()));
					$this->logger->critical(sprintf("%s - Exception: %s", __METHOD__, $e->getTraceAsString()));
					$salesConnection->rollBack();
				} finally {
					$salesConnection->commit();
				}
			}
		}
	}
}