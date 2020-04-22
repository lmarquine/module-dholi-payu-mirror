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

namespace Dholi\PayU\Model\Ui;

use Dholi\PayU\Gateway\Config\Cc\Config as CcConfig;
use Dholi\PayU\Model\Ui\Cc\ConfigProvider;
use Magento\Framework\UrlInterface;
use Magento\Vault\Api\Data\PaymentTokenInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterface;
use Magento\Vault\Model\Ui\TokenUiComponentInterfaceFactory;
use Magento\Vault\Model\Ui\TokenUiComponentProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Psr\Log\LoggerInterface;

class TokenUiComponentProvider implements TokenUiComponentProviderInterface {

	/**
	 * @var TokenUiComponentInterfaceFactory
	 */
	private $componentFactory;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	private $urlBuilder;

	private $ccConfig;

	protected $assetRepo;

	private $logger;

	public function __construct(TokenUiComponentInterfaceFactory $componentFactory,
															UrlInterface $urlBuilder,
															CcConfig $ccConfig,
															Repository $assetRepo,
															LoggerInterface $logger) {
		$this->componentFactory = $componentFactory;
		$this->urlBuilder = $urlBuilder;
		$this->ccConfig = $ccConfig;
		$this->assetRepo = $assetRepo;
		$this->logger = $logger;
	}

	/**
	 * Get UI component for token
	 * @param PaymentTokenInterface $paymentToken
	 * @return TokenUiComponentInterface
	 */
	public function getComponentForToken(PaymentTokenInterface $paymentToken) {
		$details = json_decode($paymentToken->getTokenDetails() ?: '{}', true);

		$iconType = $this->ccConfig->getIconType();
		$iconUri = "Dholi_Payment::images/payment/{$iconType}";

		$icon = [
			'url' => $this->assetRepo->getUrl("{$iconUri}/{$details['type']}.svg"),
			'height' => 30 // FIXME: colocar no config
		];
		$details['icon'] = $icon;

		$component = $this->componentFactory->create(
			[
				'config' => [
					'code' => ConfigProvider::CC_VAULT_CODE,
					'parentCode' => ConfigProvider::CODE,
					TokenUiComponentProviderInterface::COMPONENT_DETAILS => $details,
					TokenUiComponentProviderInterface::COMPONENT_PUBLIC_HASH => $paymentToken->getPublicHash()
				],
				'name' => 'Dholi_PayU/js/view/payment/method-renderer/vault'
			]
		);

		return $component;
	}
}
