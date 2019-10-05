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

use Dholi\PayU\Gateway\Config\Cc\Config as CcConfig;
use Dholi\PayU\Gateway\Config\Config;
use Dholi\PayU\Services\Payment\Pricing;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

class Installments {

	private $config;

	private $ccConfig;

	private $pricing;

	private $storeManager;

	public function __construct(Config $config,
	                            CcConfig $ccConfig,
	                            Pricing $pricing,
	                            StoreManagerInterface $storeManager) {
		$this->config = $config;
		$this->ccConfig = $ccConfig;
		$this->pricing = $pricing;
		$this->storeManager = $storeManager;
	}

	public function byAntecipacao($paymentMethod, $amount) {
		$storeId = $this->storeManager->getStore()->getId();
		$environment = $this->config->getEnvironment($storeId);
		$accountId = $this->config->getAccountId($storeId);
		$apiKey = $this->config->getApiKey($storeId);
		$publicKey = $this->config->getPublicKey($storeId);

		return $this->pricing->doPricing($environment, $paymentMethod, $amount, $accountId, $apiKey, $publicKey);
	}

	public function byFluxo($paymentMethod, $amount) {
		$storeId = $this->storeManager->getStore()->getId();

		$interest = $this->ccConfig->getCcInterest($storeId);
		$interest = floatval($interest);

		$totalInstallments = $this->ccConfig->getCcTotalInstallments($storeId);
		$installmentsWithoutInterest = $this->ccConfig->getCcInstallmentsWithoutInterest($storeId);
		$totalValue = 0;
		$j = 1;

		$pricingFees = [];
		$math = ObjectManager::getInstance()->get(\Dholi\PayU\Plugin\Math::class);

		while ($j <= $totalInstallments) {
			$paymentMode = null;
			$installmentAmout = 0;

			if ($j <= $installmentsWithoutInterest) {
				$installmentAmout = $amount / $j;
			} else {
				$installmentAmout = $math::calculatePayment($amount, $interest / 100, $j);
			}
			$totalValue = round($installmentAmout * $j, 2);

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

		$pricing = ['amount' => ['value' => $amount, 'tax' => 0, 'purchaseValue' => $amount, 'currency' => 'BRL'],
			'convertedAmount' => ['value' => $amount, 'tax' => 0, 'purchaseValue' => $amount, 'currency' => 'BRL'],
			'paymentMethodFee' => [['paymentMethod' => $paymentMethod, 'pricingFees' => $pricingFees]]
		];

		return json_encode($pricing, JSON_UNESCAPED_SLASHES);
	}

}