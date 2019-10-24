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

namespace Dholi\PayU\Resources;

class Builder {

	const FILE = __DIR__ . '/resources.yaml';

	private static $instance;

	private static $data;

	private static $path;

	private static $services;

	private function __construct() {
	}

	public static function getInstance(): \Dholi\PayU\Resources\Builder {
		if (!isset(self::$instance)) {
			self::$instance = new \Dholi\PayU\Resources\Builder();

			self::$data = \Symfony\Component\Yaml\Yaml::parseFile(self::FILE);

			self::$path = self::$data['resources']['path'];
			self::$services = self::$data['resources']['services'];
		}

		return self::$instance;
	}

	public static function getApplicationId(): int {
		if (!isset(self::$instance)) {
			self::getInstance();
		}
		return self::$data['aplication'];
	}

	public static function getUrl($resource, $environment): string {
		if (!isset(self::$instance)) {
			self::getInstance();
		}

		return sprintf("%s://%s", self::$path['protocol'], self::$path[$resource]['environment'][$environment]);
	}

	public static function getApiUrl($environment): string {
		if (!isset(self::$instance)) {
			self::getInstance();
		}
		return self::getUrl('api', $environment);
	}

	public static function getService($url, $service) {
		if (!isset(self::$instance)) {
			self::getInstance();
		}
		return sprintf("%s/%s", $url, self::$services[$service]);
	}
}