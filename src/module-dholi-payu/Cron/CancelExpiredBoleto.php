<?php
/**
* 
* PayU para Magento
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Cron;

use Dholi\PayU\Gateway\Config\Boleto\Config;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order\Payment\Repository;
use Psr\Log\LoggerInterface;

class CancelExpiredBoleto {

	private $paymentRepository;

	private $searchCriteriaBuilder;

	private $connectionPool;

	private $logger;

	private $config;

	public function __construct(LoggerInterface $logger,
	                            Repository $paymentRepository,
	                            SearchCriteriaBuilder $searchCriteriaBuilder,
	                            ResourceConnection $connectionPool = null,
	                            Config $config) {
		$this->logger = $logger;
		$this->paymentRepository = $paymentRepository;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
		$this->connectionPool = $connectionPool ?: ObjectManager::getInstance()->get(ResourceConnection::class);
		$this->config = $config;
	}

	public function execute() {
		if ($this->config->isCancelable()) {
			$searchCriteria = $this->searchCriteriaBuilder
				->addFilter('method', \Dholi\PayU\Model\Ui\Boleto\ConfigProvider::CODE, 'eq')
				->addFilter('payu_transaction_state', \Dholi\PayU\Gateway\PayU\Enumeration\PayUTransactionState::PENDING()->key(), 'eq')
				->addFilter('boleto_cancellation', date('Y-m-d H:i:s', strtotime('now')), 'lt')
				->create();

			$paymentList = $this->paymentRepository->getList($searchCriteria)->getItems();
			if (count($paymentList)) {
				$processor = ObjectManager::getInstance()->get(\Dholi\PayU\Model\PaymentManagement\Processor::class);
				$salesConnection = $this->connectionPool->getConnection('sales');

				foreach ($paymentList as $payment) {
					try {
						$salesConnection->beginTransaction();
						$processor->cancelPayment($payment);
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
}