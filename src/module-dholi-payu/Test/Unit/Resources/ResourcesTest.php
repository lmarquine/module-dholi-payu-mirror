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

namespace Dholi\PayU\Test\Unit;

use Dholi\PayU\Resources;

class BuilderTest extends \PHPUnit\Framework\TestCase {

	public function setUp(): void {

	}

	public function testApplicationId() {
		$applicationId = Builder::getInstance()->getApplicationId();

		$this->assertEquals(668978, $applicationId);
	}
}
