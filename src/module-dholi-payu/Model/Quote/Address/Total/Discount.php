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

namespace Dholi\PayU\Model\Quote\Address\Total;

use Magento\Quote\Model\QuoteValidator;
use Magento\Store\Model\StoreManagerInterface;
use Dholi\PayU\Plugin\Discount as DiscountPlugin;
use Dholi\PayU\Plugin\Math;
use Dholi\PayU\Api\Data\PaymentMethodInterface;
use Psr\Log\LoggerInterface;
use Magento\Directory\Helper\Data as DirectoryData;

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

	const CODE = 'dholi_vista';

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var QuoteValidator
	 */
	protected $quoteValidator;

	/**
	 * @var DiscountPlugin
	 */
	private $discountPlugin;

	private $storeManager;

	private $math;

	private $directoryData;

	public function __construct(LoggerInterface $logger,
	                            QuoteValidator $quoteValidator,
	                            DiscountPlugin $discountPlugin,
	                            StoreManagerInterface $storeManager,
	                            Math $math,
	                            DirectoryData $data) {
		$this->logger = $logger;
		$this->quoteValidator = $quoteValidator;
		$this->discountPlugin = $discountPlugin;
		$this->storeManager = $storeManager;
		$this->math = $math;
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

		$applyMode = $this->discountPlugin->canApply($quote);
		if ($applyMode->isApplyByRequest()) {
			$baseCurrencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
			$paymentDiscount = $this->discountPlugin->getDiscount($quote, $baseCurrencyCode);

			$baseTotalDiscountAmount = (($paymentDiscount->baseSubtotalWithDiscount + $paymentDiscount->baseTax) * $paymentDiscount->totalPercent) / 100;
			$baseTotalDiscountAmount = round($baseTotalDiscountAmount, 2);
			$totalDiscountAmount = $this->directoryData->currencyConvert($baseTotalDiscountAmount, $paymentDiscount->baseCurrencyCode);

			$total->setPayuDiscountAmount(-$totalDiscountAmount);
			$total->setPayuDiscountAmount(-$baseTotalDiscountAmount);

			$total->setGrandTotal($total->getGrandTotal() + $total->getPayuDiscountAmount());
			$total->setBaseGrandTotal($total->getBaseGrandTotal() + $total->getPayuBaseDiscountAmount());
		} else if ($applyMode->isNotApply()) {
			$total->setPayuDiscountAmount(0);
			$total->setPayuDiscountAmount(0);
		}

		return $this;
	}

	protected function clearValues(Address\Total $total) {
		$total->setTotalAmount(PaymentMethodInterface::PAYU_DISCOUNT_AMOUNT, 0);
		$total->setBaseTotalAmount(PaymentMethodInterface::PAYU_BASE_DISCOUNT_AMOUNT, 0);

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

	/**
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param Address\Total $total
	 * @return array|null
	 */
	/**
	 * Assign subtotal amount and label to address object
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param Address\Total $total
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
		return [
			'code' => $this->getCode(),
			'title' => $this->getLabel(),
			'value' => $total->getPayuDiscountAmount()
		];
	}

	/**
	 * Get Subtotal label
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getLabel() {
		return __('Discount first installment');
	}
}