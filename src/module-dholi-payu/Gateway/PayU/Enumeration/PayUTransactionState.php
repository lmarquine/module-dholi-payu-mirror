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

namespace Dholi\PayU\Gateway\PayU\Enumeration;

use Dholi\Core\Lib\Enumeration\AbstractMultiton;

class PayUTransactionState extends AbstractMultiton {

	const ERROR = 'ERROR';
	const SUCCESS = 'SUCCESS';

	public function isClientStatusError() {
		return $this->clientStatus == self::ERROR;
	}

	public function isClientStatusSuccess() {
		return $this->clientStatus == self::SUCCESS;
	}

	public function isPendind() {
		return $this->key() == 'PENDING';
	}

	public function isApproved() {
		return $this->key() == 'APPROVED';
	}

	public function isCancelled() {
		return $this->key() == 'CANCELLED';
	}

	public function isExpired() {
		return $this->key() == 'EXPIRED';
	}

	protected static function initializeMembers() {
		new static('APPROVED', PayUOrderStatus::CAPTURED(), self::SUCCESS);
		new static('CANCELLED', PayUOrderStatus::CANCELLED(), self::ERROR);
		new static('DECLINED', PayUOrderStatus::DECLINED(), self::ERROR);
		new static('EXPIRED', PayUOrderStatus::DECLINED(), self::ERROR);
		new static('ERROR', PayUOrderStatus::DECLINED(), self::ERROR);
		new static('PENDING', PayUOrderStatus::IN_PROGRESS(), self::SUCCESS);
		new static('SUBMITTED', PayUOrderStatus::IN_PROGRESS(), self::SUCCESS);
	}

	protected function __construct($key, $payuOrderStatus, $clientStatus) {
		parent::__construct($key);

		$this->payuOrderStatus = $payuOrderStatus;
		$this->clientStatus = $clientStatus;
	}

	private $payuOrderStatus;
	private $clientStatus;
}
