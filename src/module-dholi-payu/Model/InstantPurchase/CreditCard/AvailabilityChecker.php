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

namespace Dholi\PayU\Model\InstantPurchase\CreditCard;

use Magento\InstantPurchase\PaymentMethodIntegration\AvailabilityCheckerInterface;

class AvailabilityChecker implements AvailabilityCheckerInterface {

	/**
	 * @inheritdoc
	 */
	public function isAvailable(): bool {
		return true;
	}
}
