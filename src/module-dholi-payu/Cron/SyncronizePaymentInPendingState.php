<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.5
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
		$filter = new Filter();
		$filter->setField('method')->setValue('dholi_payments_payu_%')->setConditionType('like');

		$paymentGroup = new FilterGroup();
		$paymentGroup->setFilters([$filter]);

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
					$this->logger->info(sprintf("%s - Synchronizing - Order %s", __METHOD__, $payment->getOrder()->getIncrementId()));
					$processor->syncronize($payment, false, $payment->getOrder()->getGrandTotal());
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