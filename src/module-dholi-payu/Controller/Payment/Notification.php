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

namespace Dholi\PayU\Controller\Payment;

use Dholi\Core\Enumeration\HttpStatus;
use Dholi\PayU\Model\PaymentManagement\Processor;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

class Notification extends Action implements CsrfAwareActionInterface {

	private $logger;

	private $orderFactory;

	private $connectionPool;

	public function __construct(Context $context,
	                            LoggerInterface $logger,
	                            OrderFactory $orderFactory,
	                            ResourceConnection $connectionPool = null) {

		parent::__construct($context);

		$this->logger = $logger;
		$this->orderFactory = $orderFactory;
		$this->connectionPool = $connectionPool ?: ObjectManager::getInstance()->get(ResourceConnection::class);
	}

	public function execute() {
		$data = $this->getRequest()->getParams();

		$response = HttpStatus::UNPROCESSABLE_ENTITY()->getCode();
		try {

			if ($data && isset($data['reference_sale']) && isset($data['state_pol'])) {
				$order = $this->orderFactory->create()->loadByIncrementId($data['reference_sale']);

				$this->logger->info(sprintf("Processando notificação. Pedido [%s] - Status [%s].", $data['reference_sale'], $data['state_pol']));
				if ($order && !$order->isCanceled()) {
					$salesConnection = $this->connectionPool->getConnection('sales');
					$salesConnection->beginTransaction();

					$processor = ObjectManager::getInstance()->get(Processor::class);
					$processor->syncronize($order->getPayment(), false, $order->getBaseGrandTotal());

					$salesConnection->commit();
				}
				$response = HttpStatus::OK()->getCode();
			}
		} catch (CommandException $ce) {
			$this->logger->critical($ce->getMessage());
			$this->logger->critical($ce->getTraceAsString());
			$salesConnection->rollBack();

			$response = HttpStatus::INTERNAL_SERVER_ERROR()->getCode();
		}

		$this->getResponse()->clearHeader('Content-Type')->setHeader('Content-Type', 'text/html')->setHttpResponseCode($response)->setBody($response);

		return;
	}

	public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException {
		return null;
	}

	public function validateForCsrf(RequestInterface $request): ?bool {
		return true;
	}
}
