<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
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
		$message = __('Canceled order online') . ' ' . __('Transaction ID: "%1"', $payment->getTransactionId());
		$payment->getOrder()->cancel()->addStatusHistoryComment($message, false)->setIsCustomerNotified(true)->save();

		return $payment;
	}
}
