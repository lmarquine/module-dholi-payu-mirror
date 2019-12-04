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

namespace Dholi\PayU\Api\Data;

interface OrderPaymentPayUInterface {

	const PAYU_INTEREST_AMOUNT = 'payu_interest_amount';

	const PAYU_BASE_INTEREST_AMOUNT = 'payu_base_interest_amount';

	const PAYU_DISCOUNT_AMOUNT = 'payu_discount_amount';

	const PAYU_BASE_DISCOUNT_AMOUNT = 'payu_base_discount_amount';

	const TRANSACTION_STATE = 'payu_transaction_state';

	const CC_NUMBER_ENC = 'cc_number';
	const CC_CID_ENC = 'cc_cid';
}