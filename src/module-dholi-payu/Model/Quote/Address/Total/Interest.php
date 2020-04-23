<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      2.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Model\Quote\Address\Total;

use Dholi\PayU\Api\Data\OrderPaymentPayUInterface;
use Dholi\PayU\Plugin\Interest as InterestPlugin;
use Magento\Directory\Helper\Data as DirectoryData;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;

class Interest extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

	const CODE = 'dholi_interest';

	private $logger;

	private $interestPlugin;

	private $directoryData;

	public function __construct(LoggerInterface $logger,
	                            InterestPlugin $interestPlugin,
	                            DirectoryData $data) {
		$this->logger = $logger;
		$this->interestPlugin = $interestPlugin;
		$this->directoryData = $data;

		$this->setCode(self::CODE);
	}

	public function collect(\Magento\Quote\Model\Quote $quote,
	                        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
	                        \Magento\Quote\Model\Quote\Address\Total $total) {
		parent::collect($quote, $shippingAssignment, $total);

		$items = $shippingAssignment->getItems();
		if (!count($items)) {
			return $this;
		}
		$applyMode = $this->interestPlugin->canApply($quote);

		if ($applyMode->isApplyByRequest()) {
			$baseCurrencyCode = $quote->getBaseCurrencyCode();
			$paymentInterest = $this->interestPlugin->getInterest($quote, $baseCurrencyCode);

			$shippingAmount = $paymentInterest->getShippingAmount();
			$amount = ($paymentInterest->baseSubtotalWithDiscount + $paymentInterest->baseTax + $shippingAmount);

			$math = ObjectManager::getInstance()->get(\Dholi\PayU\Plugin\Math::class);
			$installmentValue = $math::calculatePayment($amount, $paymentInterest->getTotalPercent() / 100, $paymentInterest->getInstallment());
			$baseTotalInterestAmount = ($installmentValue * $paymentInterest->getInstallment()) - $amount;
			$baseTotalInterestAmount = round($baseTotalInterestAmount, 2);

			$totalInterestAmount = $this->directoryData->currencyConvert($baseTotalInterestAmount, $paymentInterest->baseCurrencyCode);

			$total->setPayuBaseInterestAmount($baseTotalInterestAmount);
			$total->setPayuInterestAmount($totalInterestAmount);

			$total->setGrandTotal($total->getGrandTotal() + $total->getPayuInterestAmount());
			$total->setBaseGrandTotal($total->getBaseGrandTotal() + $total->getPayuBaseInterestAmount());
		} else if ($applyMode->isNotApply()) {
			$total->setPayuBaseInterestAmount(0);
			$total->setPayuInterestAmount(0);
		}

		return $this;
	}

	protected function clearValues(Address\Total $total) {
		$total->setTotalAmount(OrderPaymentPayUInterface::PAYU_INTEREST_AMOUNT, 0);
		$total->setBaseTotalAmount(OrderPaymentPayUInterface::PAYU_BASE_INTEREST_AMOUNT, 0);

		$total->setTotalAmount('subtotal', 0);
		$total->setBaseTotalAmount('subtotal', 0);
		$total->setTotalAmount('tax', 0);
		$total->setBaseTotalAmount('tax', 0);
		$total->setTotalAmount('discount_tax_compensation', 0);
		$total->setBaseTotalAmount('discount_tax_compensation', 0);
		$total->setTotalAmount('shipping_discount_tax_compensation', 0);
		$total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
		$total->setSubtotalInclTax(0);
		$total->setBaseSubtotalInclTax(0);
	}

	public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
		return [
			'code' => $this->getCode(),
			'title' => $this->getLabel(),
			'value' => $total->getPayuInterestAmount()
		];
	}

	public function getLabel() {
		return __('Interest');
	}
}