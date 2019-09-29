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

namespace Dholi\PayU\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ObjectManager;

/**
 * Class Index
 * @package Dholi\PayU\Controller
 */
class Index extends Action {

	protected $resultPageFactory;

	private $logger;

	/**
	 * @var OrderPaymentRepositoryInterface
	 */
	private $orderPaymentRepository;

	private $orderRepository;

	private $connectionPool;

	/**
	 * Index constructor.
	 * @param Context $context
	 * @param JsonFactory $resultJsonFactory
	 */
	public function __construct(Context $context,
	                            PageFactory $resultPageFactory,
	                            LoggerInterface $logger,
	                            OrderRepositoryInterface $orderRepository,
	                            OrderPaymentRepositoryInterface $orderPaymentRepository,
	                            ResourceConnection $connectionPool = null) {

		parent::__construct($context);

		$this->resultPageFactory = $resultPageFactory;
		$this->logger = $logger;
		$this->orderPaymentRepository = $orderPaymentRepository;
		$this->orderRepository = $orderRepository;
		$this->connectionPool = $connectionPool ?: ObjectManager::getInstance()->get(ResourceConnection::class);
	}

	/**
	 * Function execute
	 * @return Page
	 */
	public function execute() {
		try {
			$salesConnection = $this->connectionPool->getConnection('sales');
			$salesConnection->beginTransaction();
			$order = $this->orderRepository->get(144);

			$processor = ObjectManager::getInstance()->get(\Dholi\PayU\Model\PaymentManagement\Processor::class);
			$processor->syncronize($order->getPayment(), false, $order->getBaseGrandTotal());

			$salesConnection->commit();
		} catch (\Exception $e) {
			$this->logger->critical($e->getMessage());
			$this->logger->critical($e->getTraceAsString());
			$salesConnection->rollBack();
		}

		return null;
	}
}
