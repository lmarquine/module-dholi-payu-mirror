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

namespace Dholi\PayU\Model\OrderRepository\CollectionProcessor;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class ExtensionAttributeJoinProcessor implements CollectionProcessorInterface {

	private $joinProcessor;

	public function __construct(JoinProcessorInterface $joinProcessor) {
		$this->joinProcessor = $joinProcessor;
	}

	public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection) {
		$this->joinProcessor->process($collection);
	}
}