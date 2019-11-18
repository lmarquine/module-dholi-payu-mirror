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

namespace Dholi\PayU\Plugin;

class Math {

	public static function calculatePayment($total, $interestRate, $numberOfPayments) {
		$payment = 0;
		if ($interestRate != 0) {
			// Price { R = P x [ i (1 + i)n ] ÷ [ (1 + i )n -1] }
			$payment = round($total * (($interestRate * pow((1 + $interestRate), $numberOfPayments)) / (pow((1 + $interestRate), $numberOfPayments) - 1)), 7);
		} else {
			$payment = $total / $numberOfPayments;
		}

		return $payment;
	}
}