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

namespace Dholi\PayU\Test\Unit\Connection;

use Dholi\PayU\Resources\Builder\Payment;

class JsonTest extends \PHPUnit\Framework\TestCase {

	public function setUp(): void {

	}

	public function testGet() {
		$url = Payment::getPricingUrl('production');

		$apiKey = 'LHzRB8CDNSE0HScwJxGz4i4ug0';
		$accountId = '671601';
		$publicKey = 'PK633jwAesg102D1KY85O8I56f';

		$date = gmdate("D, d M Y H:i:s", time()) . " GMT";
		$contentToSign = utf8_encode('GET' . "\n" . "\n" . "\n" . $date . "\n" . '/payments-api/rest/v4.3/pricing');
		$signature = base64_encode(hash_hmac('sha256', $contentToSign, $apiKey, true));

		$data = ['accountId' => $accountId,
			'currency' => 'BRL',
			'amount' => 189,
			'paymentMethod' => 'VISA'];

		$headers = array('Authorization' => 'Hmac ' . $publicKey . ':' . $signature,
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Date' => $date);

		$client = new \GuzzleHttp\Client();
		$client->get($url, [
			'headers' => $headers,
			'query'   => $data
		]);
	}
}
