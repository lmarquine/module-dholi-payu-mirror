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

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class VoidResponseValidator extends AbstractValidator {

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
		$subjectResponse = SubjectReader::readResponse($validationSubject);
		$transactionResponse = $subjectResponse['transaction'];

		$isValid = true;
		$errorMessages = [];
		$errorCodes = [];

		/**
		 * o cancelamento ignora erros do gateway / adquirente
		 */
		if (!$isValid) {
			$errorCodes = $this->errorCodeProvider->getErrorCodes($transactionResponse);
		}

		return $this->createResult($isValid, $errorMessages, $errorCodes);
	}
}