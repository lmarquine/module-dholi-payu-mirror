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

namespace Dholi\PayU\Gateway\Response\Payment;

use Dholi\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;

class PaymentCaptureDetailsHandler implements HandlerInterface {

	public function __construct() {
	}

	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		$result = json_decode($response[0])->result;

		if ($result->payload->transactions) {
			$transaction = $result->payload->transactions[0];
			$transactionState = PayUTransactionState::memberByKey($transaction->transactionResponse->state);

			$payment = $paymentDataObject->getPayment();
			$payment->setTransactionId($transaction->id);
			$payment->setPayuTransactionState($transactionState->key());

			$payment->setLastTransId($transaction->id);
			$payment->setAdditionalInformation('transactionId', $transaction->id);

			$payment->setIsTransactionPending($transactionState->isPendind());
			$payment->setIsTransactionApproved($transactionState->isApproved());
			$payment->setIsTransactionClosed($transactionState->isApproved());
			$payment->setShouldCloseParentTransaction($transactionState->isApproved());
		}
	}
}