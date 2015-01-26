<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class MyBase_Controller extends Public_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		Events::trigger('SHOPEVT_ShopPublicController');

		system_installed_or_die('feature_customer','/');

		$this->lang->load('nitrocart/nitrocart_my');

		// If User Not logged in
		if (!$this->current_user)
		{
			$this->session->set_flashdata(JSONStatus::Error, lang('nitrocart:my:user_not_authenticated'));

			// Send User to login then Redirect back after login
			$this->session->set_userdata('redirect_to', NC_ROUTE.'/my');
			redirect('users/login');
		}

		$this->setLayoutForShop();

	}


	private function setLayoutForShop()
	{
		$layout = 'default.html';
		$preferred = 'shop_my.html';
		$second = 'shop.html';

		if($this->template->layout_exists($preferred))
		{
			$this->template->set_layout($preferred);
		}
		else if($this->template->layout_exists($second))
		{
			$this->template->set_layout($second);
		}

	}
}