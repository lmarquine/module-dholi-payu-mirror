<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.4
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Gateway\PayU\Enumeration;

use Dholi\Core\Lib\Enumeration\AbstractMultiton;

class TransactionType extends AbstractMultiton {

	protected static function initializeMembers() {
		new static('AUTHORIZATION');
		new static('AUTHORIZATION_AND_CAPTURE');
		new static('CAPTURE');
		new static('CANCELLATION');
		new static('VOID');
		new static('REFUND');
	}

	protected function __construct($key) {
		parent::__construct($key);
	}

}