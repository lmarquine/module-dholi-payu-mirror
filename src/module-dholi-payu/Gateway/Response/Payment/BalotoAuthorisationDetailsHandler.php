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

namespace Dholi\PayU\Gateway\Response\Payment;

use Dholi\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;

class BalotoAuthorisationDetailsHandler implements HandlerInterface {

	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		$transaction = json_decode($response[0])->transactionResponse;

		$transactionState = PayUTransactionState::memberByKey($transaction->state);

		$payment = $paymentDataObject->getPayment();
		$payment->setTransactionId($transaction->transactionId);
		$payment->setPayuTransactionState($transactionState->key());

		$payment->setLastTransId($transaction->transactionId);
		$payment->setAdditionalInformation('payuOrderId', $transaction->orderId);
		$payment->setAdditionalInformation('transactionId', $transaction->transactionId);
		$payment->setAdditionalInformation('paymentLink', $transaction->extraParameters->URL_BOLETO_BANCARIO);
		$payment->setAdditionalInformation('pdfLink', $transaction->extraParameters->URL_PAYMENT_RECEIPT_PDF);
		$payment->setAdditionalInformation('barCode', $transaction->extraParameters->BAR_CODE);

		$payment->setIsTransactionPending(true);
		$payment->setIsTransactionClosed(false);
		$payment->setShouldCloseParentTransaction(false);

		$payment->getOrder()
			->setState(Order::STATE_NEW)
			->setStatus(Order::STATE_PENDING_PAYMENT)
			->setCanSendNewEmailFlag(true);
	}
}