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

namespace Dholi\PayU\Gateway\Validator\Request;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;
use Psr\Log\LoggerInterface;

class CcRequestValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator {

	protected $checkoutSession;

	private $logger;

	public function __construct(ResultInterfaceFactory $resultFactory, Session $checkoutSession, LoggerInterface $logger) {
		$this->checkoutSession = $checkoutSession;
		$this->logger = $logger;

		parent::__construct($resultFactory);
	}

	public function validate(array $validationSubject): ResultInterface {
		$isValid = true;
		$fails = array();

		$quote = $this->checkoutSession->getQuote();
		$taxvat = ($quote->getCustomerTaxvat() ? $quote->getCustomerTaxvat() : $quote->getBillingAddress()->getVatId());
		$taxvat = preg_replace('/\D/', '', $taxvat);
		if (empty($taxvat)) {
			$isValid = false;
			array_push($fails, __('Taxvat is required'));
		}
		$payment = $validationSubject['payment'];
		$creditCardHash = $payment->getAdditionalInformation(TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH);

		/**
		 * buy with credit card token
		 */
		if (null === $creditCardHash) {
			return $this->createResult($isValid, $fails);
		}

		$ccNumber = $payment->getCcNumber();
		$card = new \Dholi\Payment\Lib\CreditCard\CardNumber();
		if (!$card->passes(intval($ccNumber))) {
			$isValid = false;
			array_push($fails, $card->message());
		}

		$cvvNumber = $payment->getCcCid();
		$cvv = new \Dholi\Payment\Lib\CreditCard\CardCvc($ccNumber);
		if (!$cvv->passes(intval($cvvNumber))) {
			$isValid = false;
			array_push($fails, $cvv->message());
		}

		$date = new \Dholi\Payment\Lib\CreditCard\CardExpirationDate();
		$creditCardExpiry = $payment->getCcExpYear() . '/' . $payment->getCcExpMonth();
		if (!$date->passes($creditCardExpiry)) {
			$isValid = false;
			array_push($fails, $date->message());
		}

		return $this->createResult($isValid, $fails);
	}
}