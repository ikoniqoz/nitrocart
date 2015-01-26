<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Nitrocart extends Public_Controller
{
	private $data;

	public function __construct()
	{
		parent::__construct();	
		Events::trigger('SHOPEVT_ShopPublicController');

		$this->data = new ViewObject();
		$this->template->set_breadcrumb('Home', '/');
	}


	/**
	 * The Store home pae is driven by pages module, you need to set up a store page slug
	 * This wil
	 * @param  string $param [description]
	 * @return [type]        [description]
	 */
	public function index($param = '')
	{
		Settings::get('shop_open_status') OR redirect( NC_ROUTE . '/closed');

		redirect('/');
	}


	/*similar to shop home, first checks if shop is open, so it can redirect away*/
	public function closed()
	{
		(! Settings::get('shop_open_status') ) OR redirect( NC_ROUTE );

		$this->setLayoutForShop('nitrocart_closed.html');

		$title = Settings::get('shop_name');
		$body = Settings::get('shop_closed_reason');

		$this->template
			->title($title, 'Closed')
			->set('title',$title)
			->set('body',$body)
			->set_breadcrumb($title.' [CLOSED]')
			->build('special/closed', $this->data);
	}

	/**
	 * shop_home
	 */
	private function setLayoutForShop($preffered='nitrocart_home.html')
	{
		
		if($this->template->layout_exists($preffered))
		{
			$this->template->set_layout($preffered);
		}		
		elseif($this->template->layout_exists('nitrocart_home.html'))
		{
			$this->template->set_layout('nitrocart_home.html');
		}
		elseif($this->template->layout_exists('nitrocart.html'))
		{
			$this->template->set_layout('nitrocart.html');
		}		
	}


	/**
	 * The shop login function
	 *
	 * Point the login link to here site/shop/login and after you login you will be taken to wherever you came from.
	 *
	 * @return [type] [description]
	 */
	public function login()
	{
		// Get the page where we came from
		$url_redir = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE ;

		// Set the magic session value to redirect back
		$this->session->set_userdata('shop_force_redirect' , $url_redir );

		// Now go to login page
		redirect('users/login');
	}

}