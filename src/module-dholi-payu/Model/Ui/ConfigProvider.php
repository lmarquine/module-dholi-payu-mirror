<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.2
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Model\Ui;

use Dholi\PayU\Gateway\Config\Config;
use Dholi\PayU\Gateway\PayU\Enumeration\Country;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\View\Asset\Repository;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'dholi_payments_payu';

	protected $assetRepo;

	private $config;

	private $session;

	public function __construct(Repository $assetRepo,
	                            SessionManagerInterface $session,
	                            Config $config) {
		$this->assetRepo = $assetRepo;
		$this->session = $session;
		$this->config = $config;
	}

	public function getCode() {
		return self::CODE;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();
		$currency = $this->config->getStoreCurrency($storeId);

		return [
			'payment' => [
				self::CODE => [
					'language' => Country::memberByKey($currency)->getLanguage(),
					'url' => [
						'logo' => $this->assetRepo->getUrl('Dholi_PayU::images/logo.png')
					]
				]
			]
		];
	}
}