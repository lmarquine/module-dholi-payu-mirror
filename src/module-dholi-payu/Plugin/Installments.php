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

namespace Dholi\PayU\Plugin;

use Dholi\PayU\Gateway\Config\Cc\Config as CcConfig;
use Dholi\PayU\Gateway\Config\Config;
use Dholi\PayU\Services\Payment\Pricing;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ObjectManager;

class Installments {

	private $config;

	private $ccConfig;

	private $pricing;

	private $checkoutSession;

	public function __construct(Config $config,
	                            CcConfig $ccConfig,
	                            Pricing $pricing,
	                            Session $checkoutSession) {
		$this->config = $config;
		$this->ccConfig = $ccConfig;
		$this->pricing = $pricing;
		$this->checkoutSession = $checkoutSession;
	}

	public function byAntecipacao($paymentMethod, $amount) {
		$storeId = $this->checkoutSession->getQuote()->getStoreId();
		$environment = $this->config->getEnvironment($storeId);
		$accountId = $this->config->getAccountId($storeId);
		$apiKey = $this->config->getApiKey($storeId);
		$publicKey = $this->config->getPublicKey($storeId);
		$currencyCode = $this->checkoutSession->getQuote()->getQuoteCurrencyCode();

		return $this->pricing->doPricing($environment, $paymentMethod, $amount, $accountId, $apiKey, $publicKey, $currencyCode);
	}

	public function byFluxo($paymentMethod, $amount) {
		$storeId = $this->checkoutSession->getQuote()->getStoreId();
		$interest = $this->ccConfig->getCcInterest($storeId);
		$interest = floatval($interest);

		$totalInstallments = $this->ccConfig->getCcTotalInstallments($storeId);
		$installmentsWithoutInterest = $this->ccConfig->getCcInstallmentsWithoutInterest($storeId);
		$totalValue = 0;
		$j = 1;

		$pricingFees = [];
		$math = ObjectManager::getInstance()->get(\Dholi\PayU\Plugin\Math::class);
		$currencyCode = $this->checkoutSession->getQuote()->getQuoteCurrencyCode();

		while ($j <= $totalInstallments) {
			$paymentMode = null;
			$installmentAmout = 0;

			if ($j <= $installmentsWithoutInterest) {
				$installmentAmout = $amount / $j;
			} else {
				$installmentAmout = $math::calculatePayment($amount, $interest / 100, $j);
			}
			$totalValue = round($installmentAmout * $j, 7);

			$pricingFees[] = ['installments' => strval($j),
				'pricing' => ['payerDetail' => ['commission' => 0,
					'interest' => ($j <= $installmentsWithoutInterest ? 0 : $interest),
					'total' => $amount],
					'merchantDetail' => ['commission' => 0, 'interest' => 0, 'total' => 0],
					'totalValue' => $totalValue,
					'totalIncomeTransaction' => $totalValue]
			];
			$j++;
		}

		$pricing = ['amount' => ['value' => $amount, 'tax' => 0, 'purchaseValue' => $amount, 'currency' => $currencyCode],
			'convertedAmount' => ['value' => $amount, 'tax' => 0, 'purchaseValue' => $amount, 'currency' => $currencyCode],
			'paymentMethodFee' => [['paymentMethod' => $paymentMethod, 'pricingFees' => $pricingFees]]
		];

		return json_encode($pricing, JSON_UNESCAPED_SLASHES);
	}

}