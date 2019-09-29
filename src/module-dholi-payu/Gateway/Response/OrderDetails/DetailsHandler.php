<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Gateway\Response\OrderDetails;

use Dholi\PayU\Gateway\PayU\Enumeration\PayUOrderStatus;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class DetailsHandler implements HandlerInterface {

	public function __construct() {
	}

	/**
	 * @inheritdoc
	 */
	public function handle(array $handlingSubject, array $response) {
		$paymentDataObject = SubjectReader::readPayment($handlingSubject);
		$result = json_decode($response[0])->result;
		$payment = $paymentDataObject->getPayment();

		if ($result->payload->transactions) {
			$transaction = $result->payload->transactions[0];

			$payment->setPayuOrderStatus(PayUOrderStatus::memberByKey($result->payload->status));// transient method
			$payment->setLastTransId($transaction->id);
			$payment->setTransactionId($transaction->id);
			if(isset($transaction->parentTransactionId)) {
				$payment->setParentTransactionId($transaction->parentTransactionId);
				$payment->setTransactionId($transaction->parentTransactionId);

				$payment->setAdditionalInformation('transactionId', $transaction->id);
			}

			$payment->setPayuTransactionState($transaction->transactionResponse->state);
		}
	}
}