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

namespace Dholi\PayU\Gateway\PayU\Enumeration;

use Dholi\Core\Lib\Enumeration\AbstractMultiton;

class Mask extends AbstractMultiton {

	public function getDniMask() {
		return $this->dniMask;
	}

	public function getFoneMask() {
		return $this->foneMask;
	}

	protected static function initializeMembers() {
		new static('ARS', '', '(0099) 90000-0000');
		new static('BRL', '000.000.000-00', '(00) 90000-0000');
		new static('CLP', '', '');
		new static('COP', '000000999999', '(0) 000-0000');
		new static('MXN', '', '');
		new static('PAB', '', '');
		new static('PEN', '', '');
	}

	protected function __construct($key, $dniMask, $foneMask) {
		parent::__construct($key);

		$this->dniMask = $dniMask;
		$this->foneMask = $foneMask;
	}

	private $dniMask;
	private $foneMask;
}