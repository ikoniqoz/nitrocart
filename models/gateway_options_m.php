<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/shipping_options_m.php');
class Gateway_options_m extends Shipping_options_m 
{

	public $_type = 'gateway'; //shipping|gateway

	public function __construct()
	{
		parent::__construct();
	}

}