<?php namespace Nitrocart;

use Nitro\CommonObject;

/**
 * @author Sal Bordonaro
 */
class Test extends \Nitro\CommonObject {

	/**
	 * @constructor
	 */
	public function __construct() {
		// Get the ci instance
		$this->ci = get_instance();
	}

}