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

namespace Dholi\PayU\Gateway\Http;

use Dholi\PayU\Gateway\Config\Config;
use Dholi\PayU\Gateway\PayU\Enumeration\Country;
use Dholi\PayU\Resources\Builder\Payment;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use GuzzleHttp\Client;

class PaymentsTransferFactory implements TransferFactoryInterface {

	private $transferBuilder;

	private $config;

	private $logger;

	public function __construct(TransferBuilder $transferBuilder, Config $config, Logger $logger) {
		$this->transferBuilder = $transferBuilder;
		$this->config = $config;
		$this->logger = $logger;
	}

	/**
	 * Builds gateway transfer object
	 *
	 * @param array $request
	 * @return TransferInterface
	 */
	public function create(array $request) {
		$body = json_encode($request, JSON_UNESCAPED_SLASHES);
		$currency = $this->config->getStoreCurrency();

		$response = $this->transferBuilder
			->setMethod('POST')
			->setHeaders(['Content-Type' => 'application/json; charset=UTF-8',
				'Accept' => 'application/json',
				'Accept-Language' => Country::memberByKey($currency)->getLanguage()])
			->setBody($body)
			->setAuthUsername($this->config->getApiKey())
			->setAuthPassword($this->config->getLoginApi())
			->setUri($this->getUrl())
			->build();

		return $response;
	}

	private function getUrl() {
		$environment = $this->config->getEnvironment();
		return Payment::getPaymentsUrl($environment);
	}
}