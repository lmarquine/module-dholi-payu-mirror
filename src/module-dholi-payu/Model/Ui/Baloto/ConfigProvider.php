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

namespace Dholi\PayU\Model\Ui\Baloto;

use Dholi\PayU\Gateway\Config\Baloto\Config as BalotoConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'dholi_payments_payu_baloto';

	private $config;

	private $session;

	protected $escaper;

	public function __construct(SessionManagerInterface $session,
	                            \Magento\Framework\Escaper $escaper,
	                            BalotoConfig $balotoConfig) {
		$this->session = $session;
		$this->escaper = $escaper;
		$this->config = $balotoConfig;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();

		$payment = [];
		$isActive = $this->config->isActive($storeId);
		if ($isActive) {
			$payment = [
				self::CODE => [
					'isActive' => $isActive,
					'instructions' => $this->getInstructions($storeId)
				]
			];
		}

		return [
			'payment' => $payment
		];
	}

	protected function getInstructions($storeId): string {
		return $this->escaper->escapeHtml($this->config->getInstructions($storeId));
	}
}