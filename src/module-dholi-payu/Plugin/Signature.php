<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Plugin;

class Signature {

	const MD5_ALGORITHM = 'md5';

	const SHA_ALGORITHM = 'sha';

	const DECIMAL_POINT = '.';

	const THOUSANDS_SEPARATOR = '';

	const DECIMALS = 0;

	static function buildSignature($merchantId, $key, $value, $currency, $referenceCode, $algorithm) {
		$message = self::buildMessage($merchantId, $key, $value, $currency, $referenceCode);

		if (self::MD5_ALGORITHM == $algorithm) {
			return md5($message);
		} else if (self::SHA_ALGORITHM == $algorithm) {
			return sha1($message);
		} else {
			throw new InvalidArgumentException('Could not create signature. Invalid algoritm.');
		}
	}

	static function buildMessage($merchantId, $key, $value, $currency, $referenceCode) {
		$valueRounded = round($value, self::DECIMALS, PHP_ROUND_HALF_EVEN);
		$valueFormatted = number_format($valueRounded, self::DECIMALS, self::DECIMAL_POINT, self::THOUSANDS_SEPARATOR);

		$message = $key . '~' . $merchantId . '~' . $referenceCode . '~' . $valueFormatted . '~' . $currency;

		return $message;
	}
}