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

namespace Dholi\PayU\Model\Creditmemo\Total;

class Interest extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal {

	/**
	 * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
	 * @return $this
	 */
	public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {
		$creditmemo->setPayuInterestAmount(0);
		$creditmemo->setPayuBaseInterestAmount(0);

		$amount = $creditmemo->getOrder()->getPayuInterestAmount();
		$creditmemo->setPayuInterestAmount($amount);

		$amount = $creditmemo->getOrder()->getPayuBaseInterestAmount();
		$creditmemo->setPayuBaseInterestAmount($amount);

		$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getPayuInterestAmount());
		$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getPayuBaseInterestAmount());

		return $this;
	}
}