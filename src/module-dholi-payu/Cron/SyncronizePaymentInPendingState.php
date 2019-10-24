<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.1
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
use Magento\Sales\Model\Order\Payment\Repository;
use Psr\Log\LoggerInterface;

class SyncronizePaymentInPendingState {

	private $paymentRepository;

	private $searchCriteriaBuilder;

	private $connectionPool;

	private $logger;

	public function __construct(LoggerInterface $logger,
	                            Repository $paymentRepository,
	                            SearchCriteriaBuilder $searchCriteriaBuilder,
	                            ResourceConnection $connectionPool = null) {
		$this->logger = $logger;
		$this->paymentRepository = $paymentRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->connectionPool = $connectionPool ?: ObjectManager::getInstance()->get(ResourceConnection::class);
	}

	public function execute() {
		$filterCc = new Filter();
		$filterCc->setField('method')->setValue(\Dholi\PayU\Model\Ui\Cc\ConfigProvider::CODE)->setConditionType('eq');

		$filterBoleto = new Filter();
		$filterBoleto->setField('method')->setValue(\Dholi\PayU\Model\Ui\Boleto\ConfigProvider::CODE)->setConditionType('eq');

		$paymentGroup = new FilterGroup();
		$paymentGroup->setFilters([$filterCc, $filterBoleto]);

		// status
		$filterStatus = new Filter();
		$filterStatus->setField('payu_transaction_state')->setValue(\Dholi\PayU\Gateway\PayU\Enumeration\PayUTransactionState::PENDING()->key())->setConditionType('eq');

		$statusGroup = new FilterGroup();
		$statusGroup->setFilters([$filterStatus]);

		$searchCriteria = $this->searchCriteriaBuilder->setFilterGroups([$paymentGroup, $statusGroup])->create();
		$paymentList = $this->paymentRepository->getList($searchCriteria)->getItems();

		if (count($paymentList)) {
			$processor = ObjectManager::getInstance()->get(\Dholi\PayU\Model\PaymentManagement\Processor::class);
			$salesConnection = $this->connectionPool->getConnection('sales');

			foreach ($paymentList as $payment) {
				try {
					$salesConnection->beginTransaction();
					$processor->syncronize($payment, false, $payment->getOrder()->getBaseGrandTotal());
					$salesConnection->commit();
				} catch (\Exception $e) {
					$this->logger->critical($e->getMessage());
					$this->logger->critical($e->getTraceAsString());
					$salesConnection->rollBack();
				}
			}
		}
	}
}