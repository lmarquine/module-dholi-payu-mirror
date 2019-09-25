<?php

namespace Dholi\PayU\Gateway\PayU\Exception;

/**
 * Class InvalidCredentialsException
 *
 * @package PayU\Exception
 * @author Lucas Mendes <devsdmf@gmail.com>
 */
class InvalidCredentialsException extends PayUException {

	protected $message = 'The specified credentials are not valid, so the credentials object was not created';
}