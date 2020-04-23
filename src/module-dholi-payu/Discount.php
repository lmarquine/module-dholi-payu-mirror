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

namespace Dholi\PayU;

class Discount extends \stdClass {

	/**
	 * @var string
	 */
	public $baseCurrencyCode;

	/**
	 *
	 * @var type
	 */
	public $totalPercent;

	/**
	 *
	 * @var type
	 */
	public $baseSubtotalWithDiscount;

	/**
	 *
	 * @var type
	 */
	public $baseTax;

	public $installment;

	public function __construct() {
		$this->baseCurrencyCode = 0;
		$this->totalPercent = 0;
		$this->baseSubtotalWithDiscount = 0;
		$this->baseTax = 0;
		$this->installment = 0;
	}

	public static function getInstance($baseCurrencyCode, $totalPercent, $baseSubtotalWithDiscount, $baseTax, $installment) {
		$interest = new Discount();
		$interest->setBaseCurrencyCode($baseCurrencyCode)
			->setTotalPercent($totalPercent)
			->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount)
			->setBaseTax($baseTax)
			->setInstallment($installment);

		return $interest;
	}

	public function getTotalPercent() {
		return $this->totalPercent;
	}

	public function setTotalPercent($totalPercent) {
		$this->totalPercent = $totalPercent;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBaseCurrencyCode(): string {
		return $this->baseCurrencyCode;
	}

	public function setBaseCurrencyCode($baseCurrencyCode) {
		$this->baseCurrencyCode = $baseCurrencyCode;
		return $this;
	}

	public function getBaseSubtotalWithDiscount() {
		return $this->baseSubtotalWithDiscount;
	}

	public function setBaseSubtotalWithDiscount($baseSubtotalWithDiscount) {
		$this->baseSubtotalWithDiscount = $baseSubtotalWithDiscount;
		return $this;
	}

	public function getBaseTax() {
		return $this->baseTax;
	}

	public function setBaseTax($baseTax) {
		$this->baseTax = $baseTax;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getInstallment(): int {
		return $this->installment;
	}

	/**
	 * @param int $installment
	 */
	public function setInstallment(int $installment) {
		$this->installment = $installment;
		return $this;
	}
}
