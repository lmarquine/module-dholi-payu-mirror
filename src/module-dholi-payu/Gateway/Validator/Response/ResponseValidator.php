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

namespace Dholi\PayU\Gateway\Validator\Response;

use Dholi\PayU\Gateway\PayU\Enumeration\PayUTransactionState;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class ResponseValidator extends GeneralResponseValidator {

	/**
	 * @return array
	 */
	protected function getResponseValidators() {
		return array_merge(
			parent::getResponseValidators(),
			[
				function ($response) {
					return [
						isset($response->code) && isset($response->transactionResponse) && PayUTransactionState::memberByKey($response->transactionResponse->state)->isClientStatusSuccess(),
						[$response->transactionResponse->paymentNetworkResponseErrorMessage]
					];
				}
			]
		);
	}
}