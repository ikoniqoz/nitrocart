<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/mybase_controller.php');
class My_portal extends MyBase_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->template
			->set_breadcrumb('Home', '/')
			->set_breadcrumb(Settings::get('shop_name'), '/'.NC_ROUTE);
	}


	/**
	 *
	 *
	 * This will display a list of orders the customer
	 * has placed with the shop.
	 *
	 */
	public function index()
	{
		$en_wl = Settings::get('shop_my_wishlist_enabled');

		// Display the page
		$this->template
			->set_breadcrumb('My')
			->set('wishlist_enabled',$en_wl)
			->title( Settings::get('shop_name') )
			->build('nitrocart/my/dashboard');
	}

}