<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.5
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

	/**
	 * @inheritdoc
	 */
	public function placeRequest(TransferInterface $transferObject) {
		$submitTransaction = json_decode($transferObject->getBody(), true);

		$log = ['requestUri' => $transferObject->getUri()];

		$response = null;
		$client = $this->clientFactory->create();
		$client->setHeaders($transferObject->getHeaders());

		try {
			// token
			$createTokenResponse = null;
			if (isset($submitTransaction['creditCardToken'])) {
				$expirationDate = $submitTransaction['creditCardToken']['expirationDate'];
				$createToken = [
					'language' => $submitTransaction['language'],
					'command' => 'CREATE_TOKEN',
					'merchant' => $submitTransaction['merchant'],
					'creditCardToken' => $submitTransaction['creditCardToken']
				];
				$createToken = json_encode($createToken);
				$client->post($transferObject->getUri(), $createToken);

				$createTokenResponse = json_decode($client->getBody());

				if ($createTokenResponse && $createTokenResponse->code == 'SUCCESS') {
					if (null == $createTokenResponse->creditCardToken->expirationDate) {
						$createTokenResponse->creditCardToken->expirationDate = $expirationDate;
					}
				}

				unset($submitTransaction['creditCardToken']);
			}
			// payment
			$submitTransaction = json_encode($submitTransaction);
			$client->post($transferObject->getUri(), $submitTransaction);

			$transactionResponse = json_decode($client->getBody());
			$response = ['transaction' => $transactionResponse, 'token' => $createTokenResponse];

			$log['request'] = $this->clearBodyForLog($submitTransaction);
			$log['response'] = json_encode($response);
		} catch (\Magento\Payment\Gateway\Http\ConverterException $e) {
			throw $e;
		} finally {
			$this->logger->info(var_export($log, true));
		}

		return [$response];
	}

	private function clearBodyForLog($body) {
		if (isset($body['transaction']['order']['partnerId'])) {
			unset($body['transaction']['order']['partnerId']);
		}
		if (isset($body['transaction']['creditCard'])) {
			unset($body['transaction']['creditCard']);
		}
		if (isset($body['creditCardToken'])) {
			unset($body['creditCardToken']);
		}

		return $body;
	}

}