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

namespace Dholi\PayU\Gateway\Request\Payment;

use Dholi\PayU\Gateway\PayU\Enumeration\Country;
use Dholi\PayU\Plugin\Signature;
use Dholi\PayU\Resources\Builder;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\OrderRepository;

class AuthorizeDataBuilder implements BuilderInterface {

	const TRANSACTION = 'transaction';

	const PAYMENT_COUNTRY = 'paymentCountry';

	const TYPE = 'type';

	const ORDER = 'order';

	const ACCOUNT_ID = 'accountId';

	const REFERENCE_CODE = 'referenceCode';

	const LANGUAGE = 'language';

	const APPLICATION_ID = 'partnerId';

	const DESCRIPTION = 'description';

	const NOTIFY_URL = 'notifyUrl';

	const SIGNATURE = 'signature';

	const IP_ADDRESS = 'ipAddress';

	const BUYER = 'buyer';

	const FULL_NAME = 'fullName';

	const EMAIL_ADDRESS = 'emailAddress';

	const CONTACT_PHONE = 'contactPhone';

	const DNI_NUMBER = 'dniNumber';

	const CNPJ = 'cnpj';

	const BIRTH_DATE = 'birthdate';

	/**
	 * Address
	 */
	const SHIPPING_ADDRESS = 'shippingAddress';
	const BILLING_ADDRESS = 'billingAddress';

	const STREET_1 = 'street1';
	const STREET_2 = 'street2';
	const CITY = 'city';
	const STATE = 'state';
	const COUNTRY = 'country';
	const POSTALCODE = 'postalCode';
	const PHONE = 'phone';

	/**
	 * Payer
	 */
	const PAYER = 'payer';

	protected $urlBuilder;

	private $orderRepository;

	private $config;

	public function __construct(ConfigInterface $config,
	                            UrlInterface $urlBuilder,
	                            OrderRepository $orderRepository) {
		$this->config = $config;
		$this->urlBuilder = $urlBuilder;
		$this->orderRepository = $orderRepository;
	}

	public function build(array $buildSubject) {
		$paymentDataObject = SubjectReader::readPayment($buildSubject);
		$order = $paymentDataObject->getPayment()->getOrder();

		$storeId = $order->getStoreId();
		$billingAddress = $paymentDataObject->getOrder()->getBillingAddress();
		$shippingAddress = $paymentDataObject->getOrder()->getShippingAddress();
		if (!$shippingAddress) {
			$shippingAddress = $billingAddress;
		}

		$total = $order->getGrandTotal();
		if ($this->config->isReceiptByAntecipacao($storeId)) {
			if ($order->getPayuInterestAmount()) {
				$total -= $order->getPayuInterestAmount();
			}
		}
		$total = number_format($total, 2, '.', '');
		$currency = $order->getOrderCurrencyCode();
		$currencyEnum = Country::memberByKey($currency);

		$result = [];
		$result[self::TYPE] = \Dholi\PayU\Gateway\PayU\Enumeration\TransactionType::AUTHORIZATION_AND_CAPTURE()->key();
		$result[self::PAYMENT_COUNTRY] = $currencyEnum->getCode();
		$result[self::IP_ADDRESS] = $order->getRemoteIp();

		/**
		 * Buyer
		 */
		$name = trim($shippingAddress->getFirstname()) . ' ' . trim($shippingAddress->getLastname());
		$taxvat = ($order->getCustomerTaxvat() ? $order->getCustomerTaxvat() : $order->getBillingAddress()->getVatId());
		$taxvat = preg_replace('/\D/', '', $taxvat);
		$street = sprintf("%s, %s", substr($shippingAddress->getStreetLine1(), 0, 100), substr($shippingAddress->getStreetLine2(), 0, 100));

		$buyer = [
			self::FULL_NAME => substr($name, 0, 150),
			self::EMAIL_ADDRESS => $billingAddress->getEmail(),
			self::CONTACT_PHONE => preg_replace('/\D/', '', $shippingAddress->getTelephone()),
			self::DNI_NUMBER => $taxvat,
			self::SHIPPING_ADDRESS => [
				self::STREET_1 => $street,
				self::STREET_2 => '', //FIXME: o M2 só retorna linha 1 e 2
				self::CITY => $shippingAddress->getCity(),
				self::STATE => $shippingAddress->getRegionCode(),
				self::COUNTRY => $shippingAddress->getCountryId(),
				self::POSTALCODE => $shippingAddress->getPostcode(),
				self::PHONE => preg_replace('/\D/', '', $shippingAddress->getTelephone()),
			]
		];
		if (strlen($taxvat) == 14) {
			$buyer[self::CNPJ] = $taxvat;
		}

		/**
		 * Order
		 */
		$result[self::ORDER] = [
			self::ACCOUNT_ID => $this->config->getAccountId($storeId),
			self::REFERENCE_CODE => $order->getIncrementId(),
			self::DESCRIPTION => sprintf(__("Order %s"), $order->getIncrementId()),
			self::LANGUAGE => $currencyEnum->getLanguage(),
			self::NOTIFY_URL => $this->urlBuilder->getUrl('dholipayu/payment/notification', ['_secure' => true]),
			self::APPLICATION_ID => Builder::getInstance()->getApplicationId(),
			self::SIGNATURE => Signature::buildSignature($this->config->getMerchantId($storeId), $this->config->getApiKey($storeId), $total, $currencyEnum->getCurrency(), $order->getIncrementId(), Signature::MD5_ALGORITHM),
			'additionalValues' => [
				'TX_VALUE' => [
					'value' => $total,
					'currency' => $currencyEnum->getCurrency()
				]
			],
			self::BUYER => $buyer
		];

		/**
		 * Payer
		 */
		$payerTaxVat = $taxvat;
		$payerFone = $billingAddress->getTelephone();

		$name = trim($billingAddress->getFirstname()) . ' ' . trim($billingAddress->getLastname());
		$result[self::PAYER] = [
			self::EMAIL_ADDRESS => $billingAddress->getEmail(),
			self::FULL_NAME => $name,
			//self::BIRTH_DATE => $payerBirthDate,
			self::DNI_NUMBER => $payerTaxVat,
			self::CONTACT_PHONE => preg_replace('/\D/', '', $payerFone),
			self::BILLING_ADDRESS => [
				self::STREET_1 => substr($billingAddress->getStreetLine1(), 0, 100),
				self::STREET_2 => substr($billingAddress->getStreetLine2(), 0, 100),
				self::CITY => $billingAddress->getCity(),
				self::STATE => $billingAddress->getRegionCode(),
				self::COUNTRY => $billingAddress->getCountryId(),
				self::POSTALCODE => $billingAddress->getPostcode(),
				self::PHONE => preg_replace('/\D/', '', $payerFone),
			],
		];

		return [self::TRANSACTION => $result];
	}
}