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

namespace Dholi\PayU\Model\PaymentManagement\Operations;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class PaymentDetailsOperation {

	public function __construct() {
	}

	public function details(OrderPaymentInterface $payment) {
		$order = $payment->getOrder();

		$arguments = ['payment' => $payment, 'amount' => $order->getBaseGrandTotal()];

		if ($payment instanceof InfoInterface) {
			$arguments['payment'] = $this->paymentDataObjectFactory->create($arguments['payment']);
		}

		$method = $payment->getMethodInstance();
		$method->setStore($order->getStoreId());

		/**
		 * Muda o Magento\Payment\Model\Method\Adapter para permitir executar qualquer comando
		 */
		$reflectionClass = new \ReflectionClass($method);

		$paymentAdapter = $reflectionClass->getMethod('executeCommand');
		$paymentAdapter->setAccessible(true);
		$paymentAdapter->invoke($method, 'details', $arguments);

		return $payment;
	}
}
