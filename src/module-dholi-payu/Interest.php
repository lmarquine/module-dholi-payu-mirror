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

namespace Dholi\PayU;

class Interest extends \stdClass {

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

	private $shippingAmount;

	public function __construct() {
		$this->baseCurrencyCode = 0;
		$this->totalPercent = 0;
		$this->baseSubtotalWithDiscount = 0;
		$this->baseTax = 0;
		$this->installment = 0;
		$this->shippingAmount = 0;
	}

	public static function getInstance($baseCurrencyCode, $totalPercent, $baseSubtotalWithDiscount, $baseTax, $installment, $shippingAmount) {
		$interest = new Interest();
		$interest->setBaseCurrencyCode($baseCurrencyCode)
			->setTotalPercent($totalPercent)
			->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount)
			->setBaseTax($baseTax)
			->setInstallment($installment)
			->setShippingAmount($shippingAmount);

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

	/**
	 * @return mixed
	 */
	public function getShippingAmount() {
		return $this->shippingAmount;
	}

	/**
	 * @param mixed $shippingAmount
	 */
	public function setShippingAmount($shippingAmount) {
		$this->shippingAmount = $shippingAmount;
		return $this;
	}
}
