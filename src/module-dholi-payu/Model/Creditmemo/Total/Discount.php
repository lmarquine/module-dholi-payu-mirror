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

namespace Dholi\PayU\Model\Creditmemo\Total;

class Discount extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
		$creditmemo->setPayuDiscountAmount(0);
		$creditmemo->setPayuBaseDiscountAmount(0);

		$amount = $creditmemo->getOrder()->getPayuDiscountAmount();
		$creditmemo->setPayuDiscountAmount($amount);

		$amount = $creditmemo->getOrder()->getPayuBaseDiscountAmount();
		$creditmemo->setPayuBaseDiscountAmount($amount);

		$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getPayuDiscountAmount());
		$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getPayuBaseDiscountAmount());

		return $this;
	}
}