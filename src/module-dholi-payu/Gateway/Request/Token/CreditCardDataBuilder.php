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

namespace Dholi\PayU\Gateway\Request\Token;

use Dholi\PayU\Api\Data\OrderPaymentPayUInterface;
use Dholi\PayU\Gateway\PayU\Enumeration\PaymentMethod;
use Dholi\PayU\Gateway\Request\Payment\AuthorizeDataBuilder;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class CreditCardDataBuilder implements BuilderInterface {

	const CREDIT_CARD_TOKEN_ID = 'creditCardTokenId';

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

		$extensionAttributes = $payment->getExtensionAttributes();
		$paymentToken = $extensionAttributes->getVaultPaymentToken();
		if ($paymentToken === null) {
			throw new CommandException(__('The Payment Token is not available to perform the request.'));
		}
		$details = json_decode($paymentToken->getTokenDetails() ?: '{}');

		$payment->addData(
			[
				OrderPaymentInterface::CC_TYPE => $details->type,
				OrderPaymentInterface::CC_LAST_4 => substr($details->maskedCC, -4)
			]
		);

		return [AuthorizeDataBuilder::TRANSACTION => [
			self::CREDIT_CARD_TOKEN_ID => $paymentToken->getGatewayToken(),
			self::PAYMENT_METHOD => PaymentMethod::memberByKey($details->type)->getCode(),
			self::COOKIE => $payment->getAdditionalInformation('sessionId'),
			self::USER_AGENT => $payment->getAdditionalInformation('userAgent'),
			'extraParameters' => [
				'INSTALLMENTS_NUMBER' => $payment->getAdditionalInformation('installments')
			]
		]];
	}
}