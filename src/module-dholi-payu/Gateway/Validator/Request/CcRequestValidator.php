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

namespace Dholi\PayU\Gateway\Validator\Request;

use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Payment\Gateway\Helper\SubjectReader;

class CcRequestValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator {

	private $config;

	protected $logger;

	public function __construct(ResultInterfaceFactory $resultFactory,
	                            ConfigInterface $config,
	                            \Psr\Log\LoggerInterface $logger) {
		$this->config = $config;
		$this->logger = $logger;
		parent::__construct($resultFactory);
	}

	public function validate(array $validationSubject): ResultInterface {
		$isValid = true;
		$fails = array();

		$info = $validationSubject['payment'];
		$ccNumber = $info->getCcNumber();
		$cvvNumber = $info->getCcCid();

		$card = new \Dholi\Payment\Lib\CreditCard\CardNumber();
		if (!$card->passes(intval($ccNumber))) {
			$isValid = false;
			array_push($fails, $card->message());
		}

		$cvv = new \Dholi\Payment\Lib\CreditCard\CardCvc($ccNumber);
		if (!$cvv->passes(intval($cvvNumber))) {
			$isValid = false;
			array_push($fails, $cvv->message());
		}

		$date = new \Dholi\Payment\Lib\CreditCard\CardExpirationDate();
		$creditCardExpiry = $info->getCcExpYear() . '/' . $info->getCcExpMonth();
		if (!$date->passes($creditCardExpiry)) {
			$isValid = false;
			array_push($fails, $date->message());
		}

		return $this->createResult($isValid, $fails);
	}
}