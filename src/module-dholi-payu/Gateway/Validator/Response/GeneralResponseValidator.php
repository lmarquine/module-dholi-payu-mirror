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

namespace Dholi\PayU\Gateway\Validator\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use \Psr\Log\LoggerInterface;

class GeneralResponseValidator extends AbstractValidator {


	/**
	 * @var ErrorCodeProvider
	 */
	protected $errorCodeProvider;

	protected $logger;

	public function __construct(ResultInterfaceFactory $resultFactory,
	                            ErrorCodeProvider $errorCodeProvider,
	                            LoggerInterface $logger) {
		parent::__construct($resultFactory);

		$this->errorCodeProvider = $errorCodeProvider;
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function validate(array $validationSubject) {
		$response = json_decode(SubjectReader::readResponse($validationSubject)[0]);
		$isValid = true;
		$errorMessages = [];
		$errorCodes = [];

		foreach ($this->getResponseValidators() as $validator) {
			$validationResult = $validator($response);

			if (!$validationResult[0]) {
				$isValid = $validationResult[0];
				$errorMessages = array_merge($errorMessages, $validationResult[1]);
			}
		}
		if(!$isValid) {
			$errorCodes = $this->errorCodeProvider->getErrorCodes($response);
		}

		return $this->createResult($isValid, $errorMessages, $errorCodes);
	}

	/**
	 * @return array
	 */
	protected function getResponseValidators() {
		return [
			function ($response) {
				return [
					property_exists($response, 'code') && $response->code == 'SUCCESS',
					[__('Invalid response platform payment.')]
				];
			}
		];
	}
}