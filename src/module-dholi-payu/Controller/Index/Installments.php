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

namespace Dholi\PayU\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use Dholi\PayU\Plugin\Installments as InstallmentsPlugin;
use Dholi\Core\Enumeration\HttpStatus;

class Installments extends Action {

	protected $resultJsonFactory;

	private $installmentsPlugin;

	public function __construct(Context $context,
															JsonFactory $resultJsonFactory,
															InstallmentsPlugin $installmentsPlugin) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->installmentsPlugin = $installmentsPlugin;

		return parent::__construct($context);
	}

	public function execute() {
		$result = $this->resultJsonFactory->create();
		$response = ['code' => HttpStatus::OK()->getCode()];

		try {
			if ($this->getRequest()->isAjax()) {
				/*
				$quote = $this->getOnepage()->getQuote();
				if ($quote->isVirtual()) {
					$address = $quote->getBillingAddress();
				} else {
					$address = $quote->getShippingAddress();
				}
				$interestAmount = 0;
				$discountAmount = 0;
				if ($address) {
					$interestAmount = $address->getPayuBaseInterestAmount();
					$discountAmount = $address->getPayuBaseDiscountAmount();
				}
				$amount = ($quote->getGrandTotal() - $interestAmount) + abs($discountAmount);
				*/

				$post = $this->getRequest()->getPostValue();
				$paymentMethod = strtoupper($post['paymentMethod']);
				$receipt = $post['receipt'];
				$amount = floatval($post['amount']);

				$data = null;
				if ($receipt == 'A') {
					$data = $this->installmentsPlugin->byAntecipacao($paymentMethod, $amount);
				} else {
					$data = $this->installmentsPlugin->byFluxo($paymentMethod, $amount);
				}
				$response['data'] = $data;
			}
		} catch (\Exception $e) {
			$response['code'] = HttpStatus::BAD_GATEWAY()->getCode();
			$response['data'] = __($e->getMessage());
		}

		return $result->setData($response);
	}

	public function getOnepage() {
		return $this->_objectManager->get(\Magento\Checkout\Model\Type\Onepage::class);
	}
}
