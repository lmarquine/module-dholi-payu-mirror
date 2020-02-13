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

class ErrorCodeProvider {

	private $dynamicErrors = ['INVALID_TRANSACTION'];

	public function getErrorCodes($response): array {
		$result = [];
		if(isset($response->transactionResponse)) {
			$code = $response->transactionResponse->responseCode;
			if(in_array($code, $this->dynamicErrors)) {
				if(null !== $response->transactionResponse->responseMessage) {
					$code = $response->transactionResponse->responseMessage;
				}
			}

			$result[] = $code;
		}

		return $result;
	}
}