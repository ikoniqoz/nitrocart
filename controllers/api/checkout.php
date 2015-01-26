<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Checkout extends Public_Controller
{

	protected $section = 'api';

	public function __construct()
	{
		parent::__construct();

        // call event for extention module integration
        Events::trigger('SHOPEVT_ShopPublicController');

	}

	/**
	 * Check to see if the user can do an express checkout
	 *
	 * No need to validate keys as we are already logged in
	 * 
	 * @param [type] $id  [description]
	 * @param [type] $key [description]
	 */
	public function CurrentUserCanExpress()
	{
		$can = 0;
		$message = 'Not set';

		$this->load->helper('nitrocart/nitrocart');
		if( nc_can_express_checkout() )
		{
			$can = 1;
			$message = 'User can checkout the express way';
		}
		$response['status'] = JSONStatus::Success;
		$response['action'] = 'CurrentUserCanExpressCO';		
		$response['value'] = $can;
		$response['message'] = $message;

		echo json_encode($response);die;
	}

}
