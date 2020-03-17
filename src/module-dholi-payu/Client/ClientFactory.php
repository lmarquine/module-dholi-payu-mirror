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

namespace Dholi\PayU\Client;

class ClientFactory {

	protected $objectManager = null;

	protected $instanceName = null;

	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Dholi\\PayU\\Client\\Client') {
		$this->objectManager = $objectManager;
		$this->instanceName = $instanceName;
	}

	public function create(array $data = []) {
		return $this->objectManager->create($this->instanceName, $data);
	}
}
