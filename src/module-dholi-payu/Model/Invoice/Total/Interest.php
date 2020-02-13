<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.3
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Model\Invoice\Total;

class Interest extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Invoice $invoice
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {
		$invoice->setPayuInterestAmount(0);
		$invoice->setPayuBaseInterestAmount(0);

		$amount = $invoice->getOrder()->getPayuInterestAmount();
		$invoice->setPayuInterestAmount($amount);

		$amount = $invoice->getOrder()->getPayuBaseInterestAmount();
		$invoice->setPayuBaseInterestAmount($amount);

		$invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getPayuInterestAmount());
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getPayuBaseInterestAmount());
		return $this;
	}
}