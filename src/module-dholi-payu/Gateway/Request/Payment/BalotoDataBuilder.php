<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.3
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Gateway\Request\Payment;

use Dholi\PayU\Gateway\Config\Boleto\Config;
use Dholi\PayU\Gateway\PayU\Enumeration\PaymentMethod;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class BalotoDataBuilder implements BuilderInterface {

	const COOKIE = 'cookie';

	const USER_AGENT = 'userAgent';

	const PAYMENT_METHOD = 'paymentMethod';

	const EXPIRATION_DATE = 'expirationDate';

	private $config;

	public function __construct(Config $config) {
		$this->config = $config;
	}

	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$payment = $paymentDataObject->getPayment();
		$storeId = $payment->getOrder()->getStoreId();

		$expiration = new \DateTime('now +' . $this->config->getExpiration($storeId) . ' day');

		return [AuthorizeDataBuilder::TRANSACTION => [
			self::PAYMENT_METHOD => PaymentMethod::memberByKey('baloto')->getCode(),
			self::COOKIE => $payment->getAdditionalInformation('sessionId'),
			self::USER_AGENT => $payment->getAdditionalInformation('userAgent'),
			self::EXPIRATION_DATE => $expiration->format('Y-m-d\TH:i:s'),
			'extraParameters' => [
				'INSTALLMENTS_NUMBER' => 1
			]
		]];
	}
}