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

namespace Dholi\PayU\Connection;

use GuzzleHttp\Client;

class Json {

	/**
	 * @param $url
	 * @param $data
	 * @param $headers
	 */
	public function get($url, $data, $headers) {
		$client = new Client();
		$response = $client->get($url, [
			'headers' => array_merge(array('Content-Type' => 'application/json',
			'Accept' => 'application/json'), $headers),
			'query' => $data
		]);

		$contents = $response->getBody()->getContents();

		return $contents;
	}
}