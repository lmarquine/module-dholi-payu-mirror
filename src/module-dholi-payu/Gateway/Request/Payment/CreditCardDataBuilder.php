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

namespace Dholi\PayU\Gateway\Request\Payment;

use Dholi\PayU\Gateway\PayU\Enumeration\PaymentMethod;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;

class CreditCardDataBuilder implements BuilderInterface {

	const CREDIT_CARD = 'creditCard';

	const NUMBER = 'number';

	const SECURITY_CODE = 'securityCode';

	const EXPIRATION_DATE = 'expirationDate';

	const NAME = 'name';

	const PAYMENT_METHOD = 'paymentMethod';

	const COOKIE = 'cookie';

	const USER_AGENT = 'userAgent';

	/**
	 * Builds ENV request
	 *
	 * @param array $buildSubject
	 * @return array
	 */
	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$payment = $paymentDataObject->getPayment();

		$creditCardHash = $payment->getAdditionalInformation(TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH);

		/**
		 * buy with credit card token
		 */
		if (null === $creditCardHash) {
			//return [];
		}

		/**
		 * Credit Card
		 */
		$creditCardNumber = preg_replace('/[\-\s]+/', '', $payment->getCcNumber());
		$creditCardCvv = $payment->getCcCid();
		$creditCardExp = $payment->getCcExpYear() . '/' . $payment->getCcExpMonth();

		return [AuthorizeDataBuilder::TRANSACTION => [
			self::CREDIT_CARD => [
				self::NUMBER => $creditCardNumber,
				self::EXPIRATION_DATE => $creditCardExp,
				self::SECURITY_CODE => $creditCardCvv,
				self::NAME => $payment->getCcOwner()
			],
			self::PAYMENT_METHOD => PaymentMethod::memberByKey($payment->getCcType())->getCode(),
			self::COOKIE => $payment->getAdditionalInformation('sessionId'),
			self::USER_AGENT => $payment->getAdditionalInformation('userAgent'),
			'extraParameters' => [
				'INSTALLMENTS_NUMBER' => $payment->getAdditionalInformation('installments')
			]
		]];
	}
}