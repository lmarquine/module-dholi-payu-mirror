<?php
/**
* 
* PayU para Magento
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Model\Ui\Boleto;

use Dholi\PayU\Gateway\Config\Boleto\Config as BoletoConfig;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'dholi_payments_payu_boleto';

	private $boletoConfig;

	private $session;

	protected $escaper;

	public function __construct(SessionManagerInterface $session,
	                            \Magento\Framework\Escaper $escaper,
	                            BoletoConfig $boletoConfig) {
		$this->session = $session;
		$this->escaper = $escaper;
		$this->boletoConfig = $boletoConfig;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();

		return [
			'payment' => [
				self::CODE => [
					'isActive' => $this->boletoConfig->isActive($storeId),
					'instructions' => $this->getInstructions($storeId)
				]
			]
		];
	}

	protected function getInstructions($storeId): string {
		return $this->escaper->escapeHtml($this->boletoConfig->getInstructions($storeId));
	}
}