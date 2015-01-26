<?php namespace Nitrocart\Exceptions;

use Nitro\Exceptions\NitroException;

/**
 * @author Sal Bordonaro
 */
class ProductDeletedException extends \Nitro\Exceptions\NitroException
{
	protected $message = 'This product has been deleted';


	/**
	 * We are overriding the constructor as here we also pass in a product object
	 * This is required in order for the exception to be analuysed by the system then 
	 * disregarded and the product then returned.
	 *
	 * The next discussion point, is whether to decide if a product is deleted, is this an exception or
	 * sould it be handled differently. Deleted is more of a status rather than deleted!
	 */
	public function __construct($message=null, $code = null, $payload=null, Exception $previous = null) 
	{
		//append the custom message
		$this->message = ($message!=null) ? $message : $this->message ;
		$code = ($code!=null) ? $code : 0 ; //reset code to 0

	    //first lets give the info to the parent
	    parent::__construct($this->message, $code,$payload, $previous);

	}	

	public function getProduct()
	{
		return $this->getPayload();
	}
}