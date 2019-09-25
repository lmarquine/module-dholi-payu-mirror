<?php

namespace Dholi\PayU\Gateway\PayU\Exception;

/**
 * Class InvalidBuilderException
 *
 * @package PayU\Exception
 * @author Lucas Mendes <devsdmf@gmail.com>
 */
class InvalidBuilderException extends PayUException {

	protected $message = 'The specified builder is not valid, the builder must be an implementation of BuilderInterface';
}