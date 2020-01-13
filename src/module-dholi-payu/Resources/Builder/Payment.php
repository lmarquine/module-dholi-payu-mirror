<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.2
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Resources\Builder;

use Dholi\PayU\Resources\Builder;

class Payment {

	public static function getPaymentsUrl($environment): string {
		return Builder::getService(Builder::getUrl('api', $environment), 'payments');
	}

	public static function getReportsUrl($environment): string {
		return Builder::getService(Builder::getUrl('api', $environment), 'reports');
	}

	public static function getPricingUrl($environment): string {
		return Builder::getService(Builder::getUrl('api', $environment), 'pricing');
	}
}