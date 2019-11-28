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

namespace Dholi\PayU\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;

class Index extends Action {

	protected $resultPageFactory;

	public function __construct(Context $context, PageFactory $resultPageFactory) {
		$this->resultPageFactory = $resultPageFactory;
		return parent::__construct($context);
	}

	public function execute() {
		return $this->resultPageFactory->create();
	}
}
