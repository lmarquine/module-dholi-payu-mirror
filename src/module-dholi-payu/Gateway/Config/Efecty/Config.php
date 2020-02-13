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

namespace Dholi\PayU\Gateway\Config\Efecty;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends \Magento\Payment\Gateway\Config\Config {

	const KEY_ACTIVE = 'active';

	const KEY_INSTRUCTIONS = 'instructions';

	const EXPIRATION = 'expiration';

	private $serializer;

	public function __construct(ScopeConfigInterface $scopeConfig,
	                            $methodCode = null,
	                            $pathPattern = self::DEFAULT_PATH_PATTERN,
	                            Json $serializer = null) {
		parent::__construct($scopeConfig, $methodCode, $pathPattern);
		$this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
	}

	public function isActive($storeId = null) {
		return (bool)$this->getValue(self::KEY_ACTIVE, $storeId);
	}

	public function getInstructions($storeId = null) {
		return $this->getValue(self::KEY_INSTRUCTIONS, $storeId);
	}

	public function getExpiration($storeId = null) {
		return (int)trim($this->getValue(self::EXPIRATION, $storeId));
	}
}