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

namespace Dholi\PayU\Gateway\Config\Cc;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Config extends \Magento\Payment\Gateway\Config\Config {

	const KEY_ACTIVE = 'active';

	const ICON = 'icon';

	const KEY_CC_TOTAL_INSTALLMENTS = 'total_installmens';

	const KEY_CC_INSTALLMENTS_WITHOU_INTEREST = 'installmens_without_interest';

	const KEY_CC_INTEREST = 'interest';

	const KEY_CC_DISCOUNT = 'discount';

	const KEY_CC_MIN_INSTALLMENT = 'min_installment';

	private $serializer;

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
		$v = $this->getValue(self::KEY_CC_DISCOUNT, $storeId);
		if ($v) {
			return str_replace(',', '.', $v);
		}

		return 0.00;
	}

	public function getCcMinInstallment($storeId = null) {
		$v = $this->getValue(self::KEY_CC_MIN_INSTALLMENT, $storeId);
		if ($v) {
			return str_replace(',', '.', $v);
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
		$v = trim($this->getValue(self::KEY_CC_INTEREST, $storeId));
		if ($v) {
			return str_replace(',', '.', $v);
		}
		return $v;
	}
}