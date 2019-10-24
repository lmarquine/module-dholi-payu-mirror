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

namespace Dholi\PayU\Observer\Frontend\Quote\Address;

use Magento\Framework\DataObject\Copy;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Dholi\PayU\Api\Data\PaymentMethodInterface;
use Psr\Log\LoggerInterface;

class QuoteAddressToOrderObserver implements ObserverInterface {

	private $logger;

	private $orderRepository;

	protected $objectCopyService;

	public function __construct(OrderRepositoryInterface $orderRepository,
	                            Copy $objectCopyService,
	                            LoggerInterface $logger) {
		$this->orderRepository = $orderRepository;
		$this->objectCopyService = $objectCopyService;
		$this->logger = $logger;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		$order = $observer->getEvent()->getData('order');
		$quote = $observer->getEvent()->getData('quote');

		$shippingAddressData = $quote->getShippingAddress()->getData();
		if (isset($shippingAddressData[PaymentMethodInterface::PAYU_INTEREST_AMOUNT])) {
			$order->setPayuInterestAmount($shippingAddressData[PaymentMethodInterface::PAYU_INTEREST_AMOUNT]);
			$order->setPayuBaseInterestAmount($shippingAddressData[PaymentMethodInterface::PAYU_BASE_INTEREST_AMOUNT]);

			$order->setGrandTotal($order->getGrandTotal() + $order->getPayuInterestAmount());
			$order->setBaseGrandTotal($order->getBaseGrandTotal() + $order->getPayuBaseInterestAmount());
		}
		if (isset($shippingAddressData[PaymentMethodInterface::PAYU_DISCOUNT_AMOUNT])) {
			$order->setPayuDiscountAmount($shippingAddressData[PaymentMethodInterface::PAYU_DISCOUNT_AMOUNT]);
			$order->setPayuBaseDiscountAmount($shippingAddressData[PaymentMethodInterface::PAYU_BASE_DISCOUNT_AMOUNT]);

			$order->setGrandTotal($order->getGrandTotal() + $order->getPayuDiscountAmount());
			$order->setBaseGrandTotal($order->getBaseGrandTotal() + $order->getPayuBaseDiscountAmount());
		}

		return $this;
	}

}