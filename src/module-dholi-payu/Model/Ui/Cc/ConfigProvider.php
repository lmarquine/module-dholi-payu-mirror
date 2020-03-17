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

namespace Dholi\PayU\Model\Ui\Cc;

use Dholi\PayU\Gateway\Config\Cc\Config as CcConfig;
use Dholi\PayU\Gateway\Config\Config;
use Dholi\PayU\Resources\Builder\Payment;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Payment\Model\CcConfig as PaymentCcConfig;
use Magento\Framework\View\Asset\Repository;

class ConfigProvider implements ConfigProviderInterface {

	const CODE = 'dholi_payments_payu_cc';

	protected $urlBuilder;

	private $config;

	private $ccConfig;

	private $paymentCcConfig;

	private $session;

	protected $assetRepo;

	public function __construct(\Magento\Framework\UrlInterface $urlBuilder,
	                            SessionManagerInterface $session,
	                            Config $config,
	                            CcConfig $ccConfig,
	                            PaymentCcConfig $paymentCcConfig,
	                            Repository $assetRepo) {
		$this->session = $session;
		$this->urlBuilder = $urlBuilder;
		$this->config = $config;
		$this->ccConfig = $ccConfig;
		$this->paymentCcConfig = $paymentCcConfig;
		$this->assetRepo = $assetRepo;
	}

	public function getConfig() {
		$storeId = $this->session->getStoreId();
		$currency = $this->config->getStoreCurrency($storeId);

		$brands = [];
		$showIcon = $this->ccConfig->isShowIcon($storeId);
		if ($showIcon) {
			$iconType = $this->ccConfig->getIconType();
			$iconUri = "Dholi_Payment::images/payment/{$iconType}";
			$brands = [
				'amex' => $this->assetRepo->getUrl("{$iconUri}/amex.svg"),
				'diners' => $this->assetRepo->getUrl("{$iconUri}/diners.svg"),
				'elo' => $this->assetRepo->getUrl("{$iconUri}/elo.svg"),
				'hipercard' => $this->assetRepo->getUrl("{$iconUri}/hipercard.svg"),
				'mastercard' => $this->assetRepo->getUrl("{$iconUri}/mastercard.svg"),
				'visa' => $this->assetRepo->getUrl("{$iconUri}/visa.svg"),
			];
		}

		$payment = [];
		$isActive = $this->ccConfig->isActive($storeId);
		if($isActive) {
			$payment = [
				self::CODE => [
					'isActive' => $isActive,
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
					],
					'icons' => [
						'show' => $showIcon,
						'brands' => $brands
					],
				]
			];
		}

		return ['payment' => $payment];
	}
}