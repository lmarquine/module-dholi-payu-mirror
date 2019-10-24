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

namespace Dholi\PayU;

use Composer\Script\Event;

class Installer {

	public static function preInstall(Event $event) {
		$io = $event->getIO();
		if ($io->askConfirmation("Are you sure you want to proceed? ", false)) {
			return true;
		}
		exit;
	}
}