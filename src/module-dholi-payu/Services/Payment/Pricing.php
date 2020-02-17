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

namespace Dholi\PayU\Services\Payment;

use Dholi\PayU\Connection\Json;
use Dholi\PayU\Resources\Builder\Payment;

class Pricing {

	private $connection;

	public function __construct(Json $connection) {
		$this->connection = $connection;
	}

	public function doPricing($environment, $paymentMethod, $amount, $accountId, $apiKey, $publicKey, $currencyCode) {
		$date = gmdate("D, d M Y H:i:s", time()) . " GMT";
		$contentToSign = utf8_encode('GET' . "\n" . "\n" . "\n" . $date . "\n" . '/payments-api/rest/v4.3/pricing');
		$signature = base64_encode(hash_hmac('sha256', $contentToSign, $apiKey, true));

		$data = ['accountId' => $accountId,
			'currency' => $currencyCode,
			'amount' => $amount,
			'paymentMethod' => $paymentMethod];

		$headers = array('Authorization' => 'Hmac ' . $publicKey . ':' . $signature, 'Date' => $date);

		$url = Payment::getPricingUrl($environment);
		return $this->connection->get($url, $data, $headers);
	}
}