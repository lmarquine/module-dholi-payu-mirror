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

namespace Dholi\PayU\Api\Data;

interface PaymentMethodInterface {

	const PAYU_INTEREST_AMOUNT = 'payu_interest_amount';

	const PAYU_BASE_INTEREST_AMOUNT = 'payu_base_interest_amount';

	const PAYU_DISCOUNT_AMOUNT = 'payu_discount_amount';

	const PAYU_BASE_DISCOUNT_AMOUNT = 'payu_base_discount_amount';

	const TRANSACTION_STATE = 'payu_transaction_state';

	const CC_NUMBER_ENC = 'cc_number';
	const CC_CID_ENC = 'cc_cid';
	const CC_TYPE = 'cc_type';
	const CC_OWNER = 'cc_owner';
	const CC_LAST_4 = 'cc_last_4';
	const CC_EXP_MONTH = 'cc_exp_month';
	const CC_EXP_YEAR = 'cc_exp_year';
}