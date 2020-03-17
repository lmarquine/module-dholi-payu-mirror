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

namespace Dholi\PayU\Model\PaymentManagement;

use Dholi\PayU\Model\PaymentManagement\Operations\PaymentDetailsOperation;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class Processor {

	private $logger;

	protected $paymentDetailsOperation;

	private $searchCriteriaBuilder;

	public function __construct(LoggerInterface $logger,
	                            PaymentDetailsOperation $paymentDetailsOperation,
	                            SearchCriteriaBuilder $searchCriteriaBuilder) {

		$this->logger = $logger;
		$this->paymentDetailsOperation = $paymentDetailsOperation;
		$this->searchCriteriaBuilder = $searchCriteriaBuilder;
	}

	public function syncronize(OrderPaymentInterface $payment, $isOnline, $amount): OrderPaymentInterface {
		$payment = $this->paymentDetailsOperation->details($payment, $isOnline, $amount);
		$payuOrderStatus = $payment->getPayuOrderStatus();

		$this->logger->info(sprintf("%s - Synchronizing with PayU [%s] - Status [%s]", __METHOD__, $payment->getOrder()->getIncrementId(), $payuOrderStatus->key()));

		if ($payuOrderStatus->isCaptured()) {
			$payment = $this->capturePayment($payment);
		} elseif ($payuOrderStatus->isCancelled()) {
			$payment = $this->cancelPayment($payment);
		}

		return $payment;
	}

	private function capturePayment(OrderPaymentInterface $payment): OrderPaymentInterface {
		$message = __('Approved the payment online.') . ' ' . __('Transaction ID: "%1"', $payment->getTransactionId());
		$payment->capture()->getOrder()->addStatusHistoryComment($message, false)->setIsCustomerNotified(true);
		$payment->getOrder()->save();

		return $payment;
	}

	public function cancelPayment(OrderPaymentInterface $payment): OrderPaymentInterface {
		$order = $payment->getOrder();
		if (!$order->hasInvoices()) {
			if ($order->canCancel()) {
				$this->logger->info(sprintf("%s - Order [%s] has been canceled.", __METHOD__, $order->getIncrementId()));
				$message = __('Canceled order online') . ' ' . __('Transaction ID: "%1"', $payment->getTransactionId());

				$order->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_CANCEL, true);
				$order->addStatusHistoryComment($message, false)->setIsCustomerNotified(true);
				$order->cancel()->save();
			} else {
				$this->logger->info(sprintf("%s - Order [%s] can not be canceled.", __METHOD__, $order->getIncrementId()));
			}
		} else {
			$this->logger->info(sprintf("%s - Order [%s] has already an invoice so cannot be canceled.", __METHOD__, $order->getIncrementId()));
		}

		return $payment;
	}
}
