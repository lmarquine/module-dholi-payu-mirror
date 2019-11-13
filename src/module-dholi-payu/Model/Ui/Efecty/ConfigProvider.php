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

namespace Dholi\PayU\Model\Ui\Efecty;

use Dholi\PayU\Gateway\Config\Efecty\Config as EfectyConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'dholi_payments_payu_efecty';

	private $config;

	private $session;

	protected $escaper;

	public function __construct(SessionManagerInterface $session,
	                            \Magento\Framework\Escaper $escaper,
	                            EfectyConfig $balotoConfig) {
		$this->session = $session;
		$this->escaper = $escaper;
		$this->config = $balotoConfig;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();

		return [
			'payment' => [
				self::CODE => [
					'isActive' => $this->config->isActive($storeId),
					'instructions' => $this->getInstructions($storeId)
				]
			]
		];
	}

	protected function getInstructions($storeId): string {
		return $this->escaper->escapeHtml($this->config->getInstructions($storeId));
	}
}