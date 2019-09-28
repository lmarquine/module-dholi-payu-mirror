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

class PayUBoletoDataAssignObserver extends AbstractDataAssignObserver {

	private $logger;

	private $cookieManager;

	private $httpHeader;

	public function __construct(LoggerInterface $logger,
	                            CookieManagerInterface $cookieManager,
	                            Header $httpHeader) {
		$this->logger = $logger;
		$this->cookieManager = $cookieManager;
		$this->httpHeader = $httpHeader;
	}

	/**
	 * @param Observer $observer
	 * @return void
	 */
	public function execute(Observer $observer) {
		$paymentInfo = $this->readPaymentModelArgument($observer);
		$paymentInfo->setAdditionalInformation('sessionId', $this->cookieManager->getCookie('PHPSESSID'));
		$paymentInfo->setAdditionalInformation('userAgent', $this->httpHeader->getHttpUserAgent());

		/**
		 * Limpa dados do Cartão, se houver
		 */
		$paymentInfo->addData(
			[
				PaymentMethodInterface::CC_NUMBER_ENC => null,
				PaymentMethodInterface::CC_CID_ENC => null,
				PaymentMethodInterface::CC_TYPE => null,
				PaymentMethodInterface::CC_OWNER => null,
				PaymentMethodInterface::CC_LAST_4 => null,
				PaymentMethodInterface::CC_EXP_MONTH => null,
				PaymentMethodInterface::CC_EXP_YEAR => null
			]
		);
		//$paymentInfo->setAdditionalInformation('creditCardNumber', null);
		//$paymentInfo->setAdditionalInformation('creditCardCvv', null);
		$paymentInfo->setAdditionalInformation('installments', null);
		$paymentInfo->setAdditionalInformation('installmentAmount', null);
		$paymentInfo->setAdditionalInformation('creditCardHolderAnother', null);
		$paymentInfo->setAdditionalInformation('creditCardHolderCpf', null);
		$paymentInfo->setAdditionalInformation('creditCardHolderPhone', null);
		$paymentInfo->setAdditionalInformation('creditCardHolderBirthDate', null);
	}
}