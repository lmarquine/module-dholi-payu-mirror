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

namespace Dholi\PayU\Model\Ui\Cc;

use Dholi\PayU\Gateway\Config\Cc\Config as CcConfig;
use Dholi\PayU\Gateway\Config\Config;
use Dholi\PayU\Resources\Builder\Payment;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Payment\Model\CcConfig as PaymentCcConfig;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'dholi_payments_payu_cc';

	protected $urlBuilder;

	private $config;

	private $ccConfig;

	private $paymentCcConfig;

	private $session;

	public function __construct(\Magento\Framework\UrlInterface $urlBuilder,
	                            SessionManagerInterface $session,
	                            Config $config,
	                            CcConfig $ccConfig,
	                            PaymentCcConfig $paymentCcConfig) {
		$this->session = $session;
		$this->urlBuilder = $urlBuilder;
		$this->config = $config;
		$this->ccConfig = $ccConfig;
		$this->paymentCcConfig = $paymentCcConfig;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();

		return [
			'payment' => [
				self::CODE => [
					'isActive' => $this->ccConfig->isActive($storeId),
					'accountId' => $this->config->getAccountId($storeId),
					'publicKey' => $this->config->getPublicKey($storeId),
					'receipt' => $this->config->getReceipt($storeId),
					'minInstallment' => $this->ccConfig->getCcMinInstallment($storeId),
					'totalInstallments' => $this->ccConfig->getCcTotalInstallments($storeId),
					'percentualDiscount' => $this->ccConfig->getCcDiscount($storeId),
					'url' => [
						'cvv' => $this->paymentCcConfig->getCvvImageUrl(),
						'payments' => Payment::getPaymentsUrl($this->config->getEnvironment($storeId)),
						'installments' => $this->urlBuilder->getUrl('dholipayu/index/installments', ['_secure' => true])
					]
				]
			],
		];
	}
}