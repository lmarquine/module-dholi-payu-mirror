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

namespace Dholi\PayU\Model\Invoice\Total;

class Discount extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Invoice $invoice
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {

		$invoice->setPayuDiscountAmount(0);
		$invoice->setPayuBaseDiscountAmount(0);

		$amount = $invoice->getOrder()->getPayuDiscountAmount();
		$invoice->setPayuDiscountAmount($amount);

		$amount = $invoice->getOrder()->getPayuBaseDiscountAmount();
		$invoice->setPayuBaseDiscountAmount($amount);

		$invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getPayuDiscountAmount());
		$invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getPayuBaseDiscountAmount());

		return $this;
	}
}