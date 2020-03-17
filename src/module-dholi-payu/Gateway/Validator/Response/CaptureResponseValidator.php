<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.5
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
		$response = json_decode(SubjectReader::readResponse($validationSubject)[0]);

		$isValid = true;
		$errorMessages = [];
		$errorCodes = [];

		if (property_exists($response, 'code') && $response->code == 'ERROR') {
			$isValid = false;
			$errorMessages = array_merge($errorMessages, [$response->error]);
		}

		if ($response->result->payload->transactions) {
			$transaction = $response->result->payload->transactions[0];

			$state = PayUTransactionState::memberByKey($transaction->transactionResponse->state);
			if (!$state->isApproved()) {
				$isValid = false;
				$errorMessages = array_merge($errorMessages, [__('Transaction.State.' . $state->key())]);
			}
		}

		if (!$isValid) {
			$errorCodes = $this->errorCodeProvider->getErrorCodes($response);
		}

		return $this->createResult($isValid, $errorMessages, $errorCodes);
	}
}