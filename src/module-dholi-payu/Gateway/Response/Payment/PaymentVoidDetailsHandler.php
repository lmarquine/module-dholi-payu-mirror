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

class PaymentVoidDetailsHandler implements HandlerInterface {

	public function __construct() {
	}

	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		//$transaction = json_decode($response[0])->transactionResponse;

		$payment = $paymentDataObject->getPayment();
		/**
		 * ignora o retorno do gateway / operadora e seta o status de CANCELLED
		 */
		$payment->setPayuTransactionState(PayUTransactionState::CANCELLED()->key());
		$transactionState = PayUTransactionState::memberByKey($payment->getPayuTransactionState());

		//$payment->setTransactionId($transaction->transactionId);
		//$payment->setLastTransId($transaction->transactionId);
		//$payment->setAdditionalInformation('transactionId', $transaction->transactionId);

		$payment->setIsTransactionPending($transactionState->isPendind());
		$payment->setIsTransactionClosed($transactionState->isCancelled());
		$payment->setShouldCloseParentTransaction($transactionState->isCancelled());
	}
}