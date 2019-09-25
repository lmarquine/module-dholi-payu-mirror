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

namespace Dholi\PayU\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
use Dholi\PayU\Plugin\Installments as InstallmentsPlugin;
use Psr\Log\LoggerInterface;
use Dholi\Core\Enumeration\HttpStatus;

/**
 * Class Index
 * @package Dholi\PayU\Controller\Index\Installments
 */
class Installments extends Action {


	protected $resultJsonFactory;

	private $installmentsPlugin;

	private $logger;

	public function __construct(Context $context,
	                            JsonFactory $resultJsonFactory,
	                            InstallmentsPlugin $installmentsPlugin,
	                            LoggerInterface $logger) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->installmentsPlugin = $installmentsPlugin;
		$this->logger = $logger;

		return parent::__construct($context);
	}

	/**
	 * Function execute
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute() {
		$result = $this->resultJsonFactory->create();
		$response = ['code' => HttpStatus::OK()->getCode()];

		try {
			if ($this->getRequest()->isAjax()) {
				$quote = $this->getOnepage()->getQuote();

				$post = $this->getRequest()->getPostValue();
				$paymentMethod = strtoupper($post['paymentMethod']);
				$amount = $quote->getGrandTotal();
				$receipt = $post['receipt'];

				$data = null;
				if($receipt == 'A') {
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

	/**
	 * Get one page checkout model
	 *
	 * @return \Magento\Checkout\Model\Type\Onepage
	 * @codeCoverageIgnore
	 */
	public function getOnepage() {
		return $this->_objectManager->get(\Magento\Checkout\Model\Type\Onepage::class);
	}
}
