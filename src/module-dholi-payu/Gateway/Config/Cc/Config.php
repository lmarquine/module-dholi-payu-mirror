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

namespace Dholi\PayU\Gateway\Config\Cc;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends \Magento\Payment\Gateway\Config\Config {

	const KEY_ACTIVE = 'active';

	const ICON = 'icon';

	/**
	 * Total de Parcelas
	 */
	const KEY_CC_TOTAL_INSTALLMENTS = 'total_installmens';

	/**
	 * Parcelas sem Juros
	 */
	const KEY_CC_INSTALLMENTS_WITHOU_INTEREST = 'installmens_without_interest';

	/**
	 * Juros
	 */
	const KEY_CC_INTEREST = 'interest';

	/**
	 * Desconto à Vista
	 */
	const KEY_CC_DISCOUNT = 'discount';

	/**
	 * Parcela Mínima
	 */
	const KEY_CC_MIN_INSTALLMENT = 'min_installment';

	const ORDER_STATUS = 'order_status';

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	private $serializer;

	/**
	 *
	 * @param ScopeConfigInterface $scopeConfig
	 * @param null|string $methodCode
	 * @param string $pathPattern
	 * @param Json|null $serializer
	 */
	public function __construct(ScopeConfigInterface $scopeConfig,
	                            $methodCode = null,
	                            $pathPattern = self::DEFAULT_PATH_PATTERN,
	                            Json $serializer = null) {
		parent::__construct($scopeConfig, $methodCode, $pathPattern);
		$this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
	}

	public function isActive($storeId = null) {
		return (bool)$this->getValue(self::KEY_ACTIVE, $storeId);
	}

	public function isShowIcon($storeId = null) {
		return (bool)($this->getIconType($storeId) != 'none');
	}

	public function getIconType($storeId = null) {
		return $this->getValue(self::ICON, $storeId);
	}

	public function getCcDiscount($storeId = null) {
		$value = $this->getValue(self::KEY_CC_DISCOUNT, $storeId);
		if ($value) {
			return str_replace(',', '.', $value);
		}

		return 0.00;
	}

	public function getCcMinInstallment($storeId = null) {
		$value = $this->getValue(self::KEY_CC_MIN_INSTALLMENT, $storeId);
		if ($value) {
			return str_replace(',', '.', $value);
		}

		return 0.00;
	}

	public function getCcTotalInstallments($storeId = null) {
		return (int)$this->getValue(self::KEY_CC_TOTAL_INSTALLMENTS, $storeId);
	}

	public function getCcInstallmentsWithoutInterest($storeId = null) {
		return (int)$this->getValue(self::KEY_CC_INSTALLMENTS_WITHOU_INTEREST, $storeId);
	}

	public function getCcInterest($storeId = null) {
		$value = trim($this->getValue(self::KEY_CC_INTEREST, $storeId));
		if ($value) {
			return str_replace(',', '.', $value);
		}
		return $value;
	}
}