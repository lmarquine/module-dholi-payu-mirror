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

class VaultDataBuilder implements BuilderInterface {

	const CREDIT_CARD_TOKEN = 'creditCardToken';

	const NUMBER = 'number';

	const EXPIRATION_DATE = 'expirationDate';

	const NAME = 'name';

	const PAYMENT_METHOD = 'paymentMethod';

	const PAYER_ID = 'payerId';

	const IDENTIFICATION_NUMBER = 'identificationNumber';

	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$payment = $paymentDataObject->getPayment();

		if(!$payment->getAdditionalInformation('activePaymentToken')) {
			return [];
		}
		$order = $paymentDataObject->getPayment()->getOrder();

		$taxvat = ($order->getCustomerTaxvat() ? $order->getCustomerTaxvat() : $order->getBillingAddress()->getVatId());
		$taxvat = preg_replace('/\D/', '', $taxvat);

		$creditCardNumber = preg_replace('/[\-\s]+/', '', $payment->getCcNumber());
		$creditCardExp = $payment->getCcExpYear() . '/' . $payment->getCcExpMonth();

		return [self::CREDIT_CARD_TOKEN => [
			self::PAYER_ID => $order->getCustomerId(),
			self::NAME => $payment->getCcOwner(),
			self::IDENTIFICATION_NUMBER => $taxvat,
			self::NUMBER => $creditCardNumber,
			self::EXPIRATION_DATE => $creditCardExp,
			self::PAYMENT_METHOD => PaymentMethod::memberByKey($payment->getCcType())->getCode()
		]];
	}
}