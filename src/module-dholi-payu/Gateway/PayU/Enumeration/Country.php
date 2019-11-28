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

namespace Dholi\PayU\Gateway\PayU\Enumeration;

use Dholi\Core\Lib\Enumeration\AbstractMultiton;

class Country extends AbstractMultiton {

	public function getCode() {
		return $this->code;
	}

	public function getCurrency() {
		return $this->currency;
	}

	public function getLanguage() {
		return $this->language;
	}

	protected static function initializeMembers() {
		new static('ARS', 'AR', 'ARS', 'es');
		new static('BRL', 'BR', 'BRL', 'pt');
		new static('CLP', 'CL', 'CLP', 'es');
		new static('COP', 'CO', 'COP', 'es');
		new static('MXN', 'MX', 'MXN', 'es');
		new static('PAB', 'PA', 'PAB', 'es');
		new static('PEN', 'PE', 'PEN', 'es');
	}

	protected function __construct($key, $code, $currency, $language) {
		parent::__construct($key);

		$this->code = $code;
		$this->currency = $currency;
		$this->language = $language;
	}

	private $code;
	private $currency;
	private $language;
}