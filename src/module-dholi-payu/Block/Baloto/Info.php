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

namespace Dholi\PayU\Block\Baloto;

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
			$data[(string)__('Status')] = __('Transaction.State.' . $state);
		}
		$payuOrderId = $info->getAdditionalInformation('payuOrderId');
		if (!empty($payuOrderId)) {
			$data[(string)__('PayU Order ID')] = $payuOrderId;
		}
		$transactionId = $info->getAdditionalInformation('transactionId');
		if (!empty($transactionId)) {
			$data[(string)__('PayU Transaction ID')] = $transactionId;
		}
		$barCode = $info->getAdditionalInformation('barCode');
		if (!empty($barCode)) {
			$data[(string)__('Bar Code')] = $barCode;
		}
		$paymentLink = $info->getAdditionalInformation('paymentLink');
		if (!empty($paymentLink)) {
			$data[(string)__('Baloto Link')] = $paymentLink;
		}
		$pdfLink = $info->getAdditionalInformation('pdfLink');
		if (!empty($pdfLink)) {
			$data[(string)__('Baloto PDF')] = $pdfLink;
		}
		$responseErrors = $info->getAdditionalInformation('responseErrors');
		if (!empty($responseErrors)) {
			$data[(string)__('Error')] = $responseErrors;
		}

		return $transport->setData(array_merge($data, $transport->getData()));
	}
}
