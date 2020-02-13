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

namespace Dholi\PayU\Gateway\Request\Payment;

use Dholi\PayU\Gateway\PayU\CommandInterface;
use Dholi\PayU\Gateway\PayU\Enumeration\Country;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class GeneralDataBuilder implements BuilderInterface {

	const LANGUAGE = 'language';

	const COMMAND = 'command';

	const TEST = 'test';

	private $config;

	public function __construct(ConfigInterface $config) {
		$this->config = $config;
	}

	/**
	 * Builds ENV request
	 *
	 * @param array $buildSubject
	 * @return array
	 */
	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$order = $paymentDataObject->getPayment()->getOrder();
		$currency = $order->getOrderCurrencyCode();
		$storeId = $order->getStoreId();

		return [
			self::LANGUAGE => Country::memberByKey($currency)->getLanguage(),
			self::COMMAND => CommandInterface::PAYMENT_SUBMIT_TRANSACTION,
			self::TEST => $this->config->isInSandbox($storeId),
			'merchant' => [
				'apiKey' => $this->config->getApiKey($storeId),
				'apiLogin' => $this->config->getLoginApi($storeId)
			]
		];
	}
}