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

namespace Dholi\PayU\Gateway\Http\Client;

use Dholi\PayU\Client\ClientFactory;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Psr\Log\LoggerInterface;

class Payments implements ClientInterface {

	private $clientFactory;

	private $logger;

	public function __construct(ClientFactory $clientFactory, LoggerInterface $logger) {
		$this->clientFactory = $clientFactory;
		$this->logger = $logger;
	}

	public function placeRequest(TransferInterface $transferObject) {
		$bodyForLog = $this->clearBodyForLog($transferObject->getBody());

		$log = [
			'request' => $bodyForLog,
			'request_uri' => $transferObject->getUri()
		];
		$result = [];
		$client = $this->clientFactory->create();
		$client->setHeaders($transferObject->getHeaders());

		try {
			$client->post($transferObject->getUri(), $transferObject->getBody());

			$result = [$client->getBody()];
			$log['response'] = $client->getBody();
		} catch (\Magento\Payment\Gateway\Http\ConverterException $e) {
			throw $e;
		} finally {
			$this->logger->debug(var_export($log, true));
		}

		return $result;
	}

	private function clearBodyForLog($body) {
		$result = json_decode($body, true);
		if (isset($result['transaction']['order']['partnerId'])) {
			unset($result['transaction']['order']['partnerId']);
		}
		if (isset($result['transaction']['creditCard'])) {
			unset($result['transaction']['creditCard']);
		}

		return json_encode($result);
	}

}