<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.4
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

class RequestValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator {

	protected $checkoutSession;

	public function __construct(ResultInterfaceFactory $resultFactory, Session $checkoutSession) {
		$this->checkoutSession = $checkoutSession;

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

		return $this->createResult($isValid, $fails);
	}
}