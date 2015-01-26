<?php namespace Nitrocart\Exceptions;

use Nitro\Exceptions\NitroException;

/**
 * @author Sal Bordonaro
 */
class OrderNotFoundException extends \Nitro\Exceptions\NitroException
{
	protected $message = 'nitrocart:orders:cant_find';
}