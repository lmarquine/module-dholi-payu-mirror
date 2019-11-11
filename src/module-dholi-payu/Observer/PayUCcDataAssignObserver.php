<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      1.0.1
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Observer;

use Dholi\PayU\Api\Data\PaymentMethodInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Psr\Log\LoggerInterface;

class PayUCcDataAssignObserver extends AbstractDataAssignObserver {

	private $logger;

	private $encryptor;

	private $cookieManager;

	private $httpHeader;

	public function __construct(LoggerInterface $logger,
	                            EncryptorInterface $encryptor,
	                            CookieManagerInterface $cookieManager,
	                            Header $httpHeader) {
		$this->logger = $logger;
		$this->encryptor = $encryptor;
		$this->cookieManager = $cookieManager;
		$this->httpHeader = $httpHeader;
	}

	public function execute(Observer $observer) {
		$data = $this->readDataArgument($observer);

		$requestData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
		if (!is_array($requestData)) {
			return;
		}
		if (!is_object($requestData)) {
			$requestData = new DataObject($requestData ?: []);
		}
		$paymentInfo = $this->readPaymentModelArgument($observer);
		$paymentInfo->setAdditionalInformation('sessionId', $this->cookieManager->getCookie('PHPSESSID'));
		$paymentInfo->setAdditionalInformation('userAgent', $this->httpHeader->getHttpUserAgent());

		$ccNumber = preg_replace('/[\-\s]+/', '', $requestData->getCcNumber());
		$paymentInfo->addData(
			[
				PaymentMethodInterface::CC_NUMBER_ENC => $ccNumber,
				PaymentMethodInterface::CC_CID_ENC => $requestData->getCcCvv(),
				PaymentMethodInterface::CC_TYPE => $requestData->getCcType(),
				PaymentMethodInterface::CC_OWNER => $requestData->getCcOwner(),
				PaymentMethodInterface::CC_LAST_4 => substr($ccNumber, -4)
			]
		);
		if ($requestData->getCcExpiry() && $requestData->getCcExpiry() != '') {
			$expiry = explode("/", trim($requestData->getCcExpiry()));
			$month = trim($expiry[0]);
			$year = trim($expiry[1]);
			if (strlen($year) == 2) {
				$year = '20' . $year;
			}
			$paymentInfo->addData([
				PaymentMethodInterface::CC_EXP_MONTH => $month,
				PaymentMethodInterface::CC_EXP_YEAR => $year
			]);
		}

		if ($requestData->getCcInstallments()) {
			$arrayex = explode('-', $requestData->getCcInstallments());
			if (isset($arrayex[0])) {
				$paymentInfo->setAdditionalInformation('installments', intval($arrayex[0]));
				$paymentInfo->setAdditionalInformation('installmentAmount', floatval($arrayex[1]));
			}
		}
		if ($requestData->getCcHolderAnother() && $requestData->getCcHolderAnother() == 1) {
			$paymentInfo->setAdditionalInformation('creditCardHolderAnother', 1);
			$paymentInfo->setAdditionalInformation('creditCardHolderDninumber', $requestData->getCcHolderDninumber());
			$paymentInfo->setAdditionalInformation('creditCardHolderPhone', $requestData->getCcHolderPhone());
			$paymentInfo->setAdditionalInformation('creditCardHolderBirthDate', $requestData->getCcHolderBirthDate());
		}
	}
}