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
use Magento\Framework\App\Action\Action;

/**
 * Class Index
 * @package Dholi\PayU\Controller\Index\Index
 */
class Index extends Action {


	/**
	 * Index resultPageFactory
	 * @var PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * Index constructor.
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(Context $context, PageFactory $resultPageFactory) {
		$this->resultPageFactory = $resultPageFactory;
		return parent::__construct($context);
	}

	/**
	 * Function execute
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute() {
		return $this->resultPageFactory->create();
	}
}
