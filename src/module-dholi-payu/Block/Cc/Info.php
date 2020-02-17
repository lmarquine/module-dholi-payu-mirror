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

namespace Dholi\PayU\Block\Cc;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

class Info extends ConfigurableInfo {

	protected function getLabel($field) {
		return __($field);
	}

	protected function _prepareSpecificInformation($transport = null) {
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		$transport = parent::_prepareSpecificInformation($transport);
		$info = $this->getInfo();
		$data = array();

		$state = $info->getPayuTransactionState();
		if (!empty($state)) {
			$data[(string)__('Transaction State')] = __('Transaction.State.' . $state);
		}
		$payuOrderId = $info->getAdditionalInformation('payuOrderId');
		if (!empty($payuOrderId)) {
			$data[(string)__('PayU Order ID')] = $payuOrderId;
		}
		$transactionId = $info->getAdditionalInformation('transactionId');
		if (!empty($transactionId)) {
			$data[(string)__('PayU Transaction ID')] = $transactionId;
		}
		if ($ccType = $info->getCcType()) {
			$data[(string)__('Credit Card Type')] = $ccType;
		}
		if ($info->getCcLast4()) {
			$data[(string)__('Last Credit Card Number')] = sprintf('xxxx xxxx xxxx %s', $info->getCcLast4());
		}
		$installments = $info->getAdditionalInformation('installments');
		if (!empty($installments)) {
			$data[(string)__('Installments')] = __("In %1x of %2", $installments, $this->getFormattedInstallmentAmount($info->getAdditionalInformation('installmentAmount')));
		}
		$responseErrors = $info->getAdditionalInformation('responseErrors');
		if (!empty($responseErrors)) {
			$data[(string)__('Error')] = $responseErrors;
		}

		return $transport->setData(array_merge($data, $transport->getData()));
	}

	private function getFormattedInstallmentAmount($installmentAmount) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceCurrency = $objectManager->create('Magento\Framework\Pricing\PriceCurrencyInterface');

		return $priceCurrency->format($installmentAmount, false);
	}
}
