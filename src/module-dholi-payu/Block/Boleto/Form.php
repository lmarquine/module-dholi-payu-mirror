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

namespace Dholi\PayU\Block\Boleto;

use Dholi\PayU\Gateway\Config\Cc\Config;
use Magento\Payment\Helper\Data;

/**
 * Class Form
 */
class Form extends \Magento\Payment\Block\Form {

	/**
	 * @var Config
	 */
	protected $gatewayConfig;

	/**
	 * @var Data
	 */
	private $paymentDataHelper;

	public function __construct(Config $gatewayConfig,
	                            Data $paymentDataHelper) {
		$this->gatewayConfig = $gatewayConfig;
		$this->paymentDataHelper = $paymentDataHelper;
	}
}
