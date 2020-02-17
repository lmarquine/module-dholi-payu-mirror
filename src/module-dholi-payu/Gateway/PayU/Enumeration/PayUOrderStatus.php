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

class PayUOrderStatus extends AbstractMultiton {

	public function isCaptured() {
		return ($this->key() == 'CAPTURED');
	}

	public function isCancelled() {
		return ($this->key() == 'CANCELLED' || $this->key() == 'DECLINED');
	}

	protected static function initializeMembers() {
		new static('NEW');
		new static('IN_PROGRESS');
		new static('AUTHORIZED');
		new static('CAPTURED');
		new static('CANCELLED');
		new static('DECLINED');
		new static('REFUNDED');
	}

	protected function __construct($key) {
		parent::__construct($key);
	}
}
