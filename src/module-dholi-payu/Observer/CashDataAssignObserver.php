<?php
/**
* 
* PayU para Magento 2
* 
* @category     Dholi
* @package      Modulo PayU
* @copyright    Copyright (c) 2019 dholi (https://www.dholi.dev)
* @version      2.0.0
* @license      https://www.dholi.dev/license/
*
*/
declare(strict_types=1);

namespace Dholi\PayU\Observer;

use Dholi\PayU\Api\Data\OrderPaymentPayUInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Psr\Log\LoggerInterface;

class CashDataAssignObserver extends AbstractDataAssignObserver {

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

	public function execute(Observer $observer) {
		$paymentInfo = $this->readPaymentModelArgument($observer);
		$paymentInfo->setAdditionalInformation('sessionId', $this->cookieManager->getCookie('PHPSESSID'));
		$paymentInfo->setAdditionalInformation('userAgent', $this->httpHeader->getHttpUserAgent());

		/**
		 * Limpa dados do Cartão, se houver
		 */
		$paymentInfo->addData(
			[
				OrderPaymentPayUInterface::CC_NUMBER_ENC => null,
				OrderPaymentPayUInterface::CC_CID_ENC => null,
				OrderPaymentInterface::CC_TYPE => null,
				OrderPaymentInterface::CC_OWNER => null,
				OrderPaymentInterface::CC_LAST_4 => null,
				OrderPaymentInterface::CC_EXP_MONTH => null,
				OrderPaymentInterface::CC_EXP_YEAR => null
			]
		);
		$paymentInfo->setAdditionalInformation('installments', null);
		$paymentInfo->setAdditionalInformation('installmentAmount', null);
	}
}