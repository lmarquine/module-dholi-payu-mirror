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

namespace Dholi\PayU\Block\Checkout\Success;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;

class Additional extends \Magento\Framework\View\Element\Template {

	private $paymentMethod;
	private $checkoutSession;
	protected $orderFactory;
	protected $order;

	public function __construct(Context $context,
	                            Session $checkoutSession,
	                            OrderFactory $orderFactory,
	                            array $data = []) {
		parent::__construct($context, $data);
		$this->checkoutSession = $checkoutSession;
		$this->orderFactory = $orderFactory;

		if (!$this->isBoleto() && !$this->isCc()) {
			return;
		}
		$this->setTemplate("Dholi_PayU::checkout/success/additional.phtml");
	}

	public function getPayment() {
		if ($this->paymentMethod == null) {
			$this->paymentMethod = $this->getOrder()->getPayment();
		}

		return $this->paymentMethod;
	}

	public function getOrder() {
		if ($this->order == null) {
			$this->order = $this->orderFactory->create()->load($this->checkoutSession->getLastOrderId());
		}
		return $this->order;
	}

	public function isBoleto(): bool {
		$method = $this->getPayment()->getMethod();
		if ($method == \Dholi\PayU\Model\Ui\Boleto\ConfigProvider::CODE) {
			return true;
		}

		return false;
	}

	public function isCc(): bool {
		$method = $this->getPayment()->getMethod();
		if ($method == \Dholi\PayU\Model\Ui\Cc\ConfigProvider::CODE) {
			return true;
		}

		return false;
	}

	public function getBoletoLink() {
		$paymentLink = $this->getPayment()->getAdditionalInformation('paymentLink');
		if ($paymentLink) {
			return $paymentLink;
		}

		return null;
	}

	public function getBoletoPdfLink() {
		$pdfLink = $this->getPayment()->getAdditionalInformation('pdfLink');
		if ($pdfLink) {
			return $pdfLink;
		}

		return null;
	}

	public function getBoletoDateOfExpiration() {
		$dateOfExpiration = $this->getPayment()->getAdditionalInformation('dateOfExpiration');
		if ($dateOfExpiration) {
			return $dateOfExpiration;
		}

		return null;
	}

	public function getOrderId() {
		$payuOrderId = $this->getPayment()->getAdditionalInformation('payuOrderId');
		if ($payuOrderId) {
			return $payuOrderId;
		}
		return null;
	}

	public function getTransactionId() {
		$transactionId = $this->getPayment()->getAdditionalInformation('transactionId');
		if ($transactionId) {
			return $transactionId;
		}
		return null;
	}

	public function getBoletoBarcode() {
		$barCode = $this->getPayment()->getAdditionalInformation('barCode');
		if ($barCode) {
			return $barCode;
		}

		return null;
	}

	public function getCcType() {
		$value = $this->getPayment()->getCcType();
		if ($value) {
			return $value;
		}

		return null;
	}

	public function getCcLast4() {
		$value = $this->getPayment()->getCcLast4();
		if ($value) {
			return $value;
		}

		return null;
	}

	public function getPayuTransactionState() {
		$value = $this->getPayment()->getPayuTransactionState();
		if ($value) {
			return $value;
		}

		return null;
	}

	public function getFormattedInstallmentAmount() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceCurrency = $objectManager->create('Magento\Framework\Pricing\PriceCurrencyInterface');

		$installmentAmount = $this->getPayment()->getAdditionalInformation('installmentAmount');
		$installments = $this->getPayment()->getAdditionalInformation('installments');

		return __("In %1x of %2", $installments, $priceCurrency->format($installmentAmount, false));
	}

	public function hasInstallments() {
		$installments = $this->getPayment()->getAdditionalInformation('installments');

		return (null != $installments && !empty($installments));
	}

}