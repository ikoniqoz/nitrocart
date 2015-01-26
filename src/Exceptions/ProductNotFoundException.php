<?php namespace Nitrocart\Exceptions;

use Nitro\Exceptions\NitroException;

/**
 * @author Sal Bordonaro
 */
class ProductNotFoundException extends \Nitro\NitroException
{
	protected $message = 'nitrocart:products:cant_find';
}