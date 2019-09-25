<?php
/**
* 
* PayU para Magento
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Gateway\Validator\Response;
use Dholi\PayU\Gateway\PayU\Result\Error;

class ErrorCodeProvider {

	public function getErrorCodes($response): array {
		$result = [];
		if(isset($response->transactionResponse)) {
			$result[] = $response->transactionResponse->responseCode;
		}

		return $result;
	}
}