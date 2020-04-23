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

namespace Dholi\PayU\Gateway\Validator\Response;

use Dholi\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class CaptureResponseValidator extends AbstractValidator {

	protected $errorCodeProvider;

	public function __construct(ResultInterfaceFactory $resultFactory,
	                            ErrorCodeProvider $errorCodeProvider) {
		parent::__construct($resultFactory);

		$this->errorCodeProvider = $errorCodeProvider;
	}

	/**
	 * @inheritdoc
	 */
	public function validate(array $validationSubject) {
		$subjectResponse = SubjectReader::readResponse($validationSubject)[0];
		$transactionResponse = $subjectResponse['transaction'];

		$isValid = true;
		$errorMessages = [];
		$errorCodes = [];

		if (property_exists($transactionResponse, 'code') && $transactionResponse->code == 'ERROR') {
			$isValid = false;
			$errorMessages = array_merge($errorMessages, [$transactionResponse->error]);
		}

		if ($transactionResponse->result->payload->transactions) {
			$transaction = $transactionResponse->result->payload->transactions[0];

			$state = PayUTransactionState::memberByKey($transaction->transactionResponse->state);
			if (!$state->isApproved()) {
				$isValid = false;
				$errorMessages = array_merge($errorMessages, [__('Transaction.State.' . $state->key())]);
			}
		}

		if (!$isValid) {
			$errorCodes = $this->errorCodeProvider->getErrorCodes($transactionResponse);
		}

		return $this->createResult($isValid, $errorMessages, $errorCodes);
	}
}