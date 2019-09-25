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

namespace Dholi\PayU\Model\Quote\Address\Total;

use Dholi\PayU\Api\Data\PaymentMethodInterface;
use Dholi\PayU\Plugin\Interest as InterestPlugin;
use Magento\Directory\Helper\Data as DirectoryData;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\QuoteValidator;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Interest extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

	const CODE = 'dholi_interest';

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var QuoteValidator
	 */
	protected $quoteValidator;

	/**
	 * @var InterestPlugin
	 */
	private $interestPlugin;

	private $storeManager;

	private $directoryData;

	public function __construct(LoggerInterface $logger,
	                            QuoteValidator $quoteValidator,
	                            InterestPlugin $interestPlugin,
	                            StoreManagerInterface $storeManager,
	                            DirectoryData $data) {
		$this->logger = $logger;
		$this->quoteValidator = $quoteValidator;
		$this->interestPlugin = $interestPlugin;
		$this->storeManager = $storeManager;
		$this->directoryData = $data;

		$this->setCode(self::CODE);
	}

	public function collect(
		\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
	) {
		parent::collect($quote, $shippingAssignment, $total);

		$items = $shippingAssignment->getItems();
		if (!count($items)) {
			return $this;
		}
		$applyMode = $this->interestPlugin->canApply($quote);

		if ($applyMode->isApplyByRequest()) {
			$baseCurrencyCode = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
			$paymentInterest = $this->interestPlugin->getInterest($quote, $baseCurrencyCode);

			$shippingAmount = $paymentInterest->getShippingAmount();
			$amount = ($paymentInterest->baseSubtotalWithDiscount + $paymentInterest->baseTax + $shippingAmount);

			$math = ObjectManager::getInstance()->get(\Dholi\PayU\Plugin\Math::class);
			$installmentValue = $math::calculatePayment($amount, $paymentInterest->getTotalPercent() / 100, $paymentInterest->getInstallment());
			$baseTotalInterestAmount = ($installmentValue * $paymentInterest->getInstallment()) - $amount;
			$baseTotalInterestAmount = round($baseTotalInterestAmount, 2);

			$totalInterestAmount = $this->directoryData->currencyConvert($baseTotalInterestAmount, $paymentInterest->baseCurrencyCode);

			$total->setPayuInterestAmount($totalInterestAmount);
			$total->setPayuBaseInterestAmount($baseTotalInterestAmount);

			$total->setGrandTotal($total->getGrandTotal() + $total->getPayuInterestAmount());
			$total->setBaseGrandTotal($total->getBaseGrandTotal() + $total->getPayuBaseInterestAmount());
		} else if ($applyMode->isNotApply()) {
			$total->setPayuInterestAmount(0);
			$total->setPayuBaseInterestAmount(0);
		}

		return $this;
	}

	protected function clearValues(Address\Total $total) {
		$total->setTotalAmount(PaymentMethodInterface::PAYU_INTEREST_AMOUNT, 0);
		$total->setBaseTotalAmount(PaymentMethodInterface::PAYU_BASE_INTEREST_AMOUNT, 0);

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
			'value' => $total->getPayuInterestAmount()
		];
	}

	/**
	 * Get Subtotal label
	 *
	 * @return \Magento\Framework\Phrase
	 */
	public function getLabel() {
		return __('Interest');
	}
}