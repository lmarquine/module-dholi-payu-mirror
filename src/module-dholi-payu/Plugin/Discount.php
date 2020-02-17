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

namespace Dholi\PayU\Plugin;

use Dholi\PayU\Gateway\Config\Cc\Config;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class Discount {

	const METHOD = 'method';

	const INSTALLMENTS = 'cc_installments';

	private $logger;

	private $request;

	private $config;

	public function __construct(LoggerInterface $logger,
	                            RequestInterface $request,
	                            Config $config) {
		$this->logger = $logger;
		$this->request = $request;
		$this->config = $config;
	}

	public function canApply(\Magento\Quote\Model\Quote $quote) {
		$applyMode = new \Dholi\Payment\TotalsApllyMode();

		$currentPaymentMethod = null;
		$data = $this->request->getPost('payment');
		if (isset($data[self::METHOD])) { // vem da escolha das parcelas
			$currentPaymentMethod = $data[self::METHOD];

			if (isset($data[self::INSTALLMENTS])) {
				$arrayex = explode('-', $data[self::INSTALLMENTS]);
				if (count($arrayex) < 2) {
					return $applyMode->notApply();
				}
				$installments = intval($arrayex[0]);
				if ($currentPaymentMethod == \Dholi\PayU\Model\Ui\Cc\ConfigProvider::CODE) {
					if ($installments == 1) {
						return $applyMode->applyByRequest();
					}
				}
			}
		} else {
			if ($quote->getPayment() != null && $quote->getPayment()->hasMethodInstance()) {
				$currentPaymentMethod = $quote->getPayment()->getMethodInstance()->getCode();
			}
			if ($currentPaymentMethod == \Dholi\PayU\Model\Ui\Cc\ConfigProvider::CODE) {
				return $applyMode->applyByTotals();
			}
		}

		return $applyMode->notApply();
	}

	public function getDiscount(\Magento\Quote\Model\Quote $quote, $baseCurrencyCode) {
		$data = $this->request->getPost('payment');
		if (isset($data[self::INSTALLMENTS])) {
			$arrayex = explode('-', $data[self::INSTALLMENTS]);
			if (count($arrayex) == 2) {
				$installments = intval($arrayex[0]);
				$baseSubtotalWithDiscount = 0;
				$baseTax = 0;

				if ($quote->isVirtual()) {
					$address = $quote->getBillingAddress();
				} else {
					$address = $quote->getShippingAddress();
				}
				if ($address) {
					$baseSubtotalWithDiscount = $address->getBaseSubtotalWithDiscount();
					$baseTax = $address->getBaseTaxAmount();
				}
				$discount = $this->config->getCcDiscount();

				return \Dholi\PayU\Discount::getInstance($baseCurrencyCode, $discount, $baseSubtotalWithDiscount, $baseTax, $installments);
			}
		}
	}
}