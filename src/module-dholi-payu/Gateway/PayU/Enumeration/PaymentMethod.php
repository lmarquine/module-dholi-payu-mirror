<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      2.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Gateway\PayU\Enumeration;

use Dholi\Core\Lib\Enumeration\AbstractMultiton;

class PaymentMethod extends AbstractMultiton {

	public function getCode() {
		return $this->code;
	}

	protected static function initializeMembers() {
		/**
		 * LATAM
		 */
		new static('visa', 'VISA');
		new static('elo', 'ELO');
		new static('hipercard', 'HIPERCARD');
		new static('mastercard', 'MASTERCARD');
		new static('amex', 'AMEX');
		new static('dinersclub', 'DINERS');

		/**
		 * BR
		 */
		new static('boleto', 'BOLETO_BANCARIO');

		/**
		 * CO
		 */
		new static('baloto', 'BALOTO');
		new static('efecty', 'EFECTY');
		new static('sured', 'SURED');
	}

	protected function __construct($key, $code) {
		parent::__construct($key);

		$this->code = $code;
	}

	private $code;
}