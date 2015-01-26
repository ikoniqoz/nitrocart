<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/mybase_controller.php');
class Wishlist extends MyBase_Controller
{
	protected $data;

	public function __construct()
	{
		parent::__construct();

		$this->data = new ViewObject();


		//$this->data->en_wl = $this->settings_m->get_by( ['slug' => 'shop_my_wishlist_enabled']);

		//($this->data->en_wl->value == 1) OR redirect( NC_ROUTE.'/my');

		$this->load->model('nitrocart/wishlist_m');

		$this->template
			->set_breadcrumb('Home', '/')
			->set_breadcrumb( Settings::get('shop_name') , '/'.NC_ROUTE)
			->set_breadcrumb('My', NC_ROUTE.'/my');

	}


	/**
	 *
	 *
	 * Show the main dashboard menu and also display some usefull summary information about
	 * their transactions ect.
	 */
	public function index()
	{

		$data = (object) array();

		$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/my/wishlist';

		$data->items = $this->wishlist_m->get_many_by('nct_wishlist.user_id', $this->current_user->id );

		$this->template
			->set_breadcrumb(lang('nitrocart:my:wishlist'))
			->title( Settings::get('shop_name') )
			->build('my/wishlist', $data);

	}


	public function delete($id = 0)
	{
		$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/my/wishlist';
		$this->_wishlist_delete($id);
	}



	public function add($product_id = 0)
	{

		// Load Libraries
		$this->load->model('nitrocart/products_front_m');


		// prepare redirect
		$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/my/wishlist';


		// Get the product ID - First check if posted, if not use the direct product_id passed in
		$product_id = $this->input->post('product_id') ? $this->input->post('product_id') : $product_id;


		//
		// Validate the Item for the wishlist
		//
		if($prod = $this->_wishlist_preadd($product_id) )
		{
			$prod->price = 0;
			// if all good add it to the db
			$this->wishlist_m->add($this->current_user->id, $prod); // pass the price of product at time of adding (historical data)

			$this->session->set_flashdata( JSONStatus::Success ,  'Item has been added to Wishlist'  );
		}
		else
		{
			//flash message from validation
		}


		redirect($redirect);
	}


	/**
	 * Adds a product Item to the wishlist
	 * To access this, use the wishlist method : site.com/shop/my/wishlist/add/PROD_ID
	 *
	 * @param $product_id 	The ID of the product that is being requested to add to the wishlist
	 * @access private
	 * @return Mixed (false|Product [Object] )
	 */
	private function _wishlist_preadd($product_id = 0)
	{

		// Load Libraries
		$this->load->model('nitrocart/products_front_m');



		// Get the product ID - First check if posted, if not use the direct product_id passed in
		$product_id = $this->input->post('product_id') ? $this->input->post('product_id') : $product_id;



		// Check validity of product ID
		if ( (is_numeric($product_id)) && ($product_id <= 0) )
		{
			// If not numeric stop and return
			$this->session->set_flashdata( JSONStatus::Error , 'Invalid Product Data');
			return false;
		}


		if( ! $this->current_user )
		{
			$this->session->set_flashdata( JSONStatus::Error ,  'You must first login' );
			return false;
		}



		//
		// Check if the item already exist - do this before fetching the Item
		//
		if ($this->wishlist_m->item_exist( $this->current_user->id, $product_id))
		{
			$this->session->set_flashdata( JSONStatus::Error ,  lang('nitrocart:my:already_in_wishlist') );
			return false;
		}



		// Get the product from DB
		$product = $this->pyrocache->model('products_front_m', 'get_product', $product_id);



		// Check if the produyct exist in the DB
		if(!$product)
		{
			$this->session->set_flashdata( JSONStatus::Error ,  lang('nitrocart:my:product_not_found') );
			return false;
		}



		// Check product validady (visible or deleted)
		if ( $product->deleted != NULL || ($product->public == 0))
		{
			//$this->session->set_flashdata( JSONStatus::Error ,  lang('nitrocart:my:product_unavailable') );
			//return false;
		}


		// OK to add now if it passes the above test, return the object
		return $product;
	}



	/**
	 * To access this, use the wishlist method : site.com/shop/my/wishlist/del/PROD_ID
	 *
	 * @param INT $product_id
	 * @access private
	 */
	private function _wishlist_delete($product_id = 0)
	{

		$this->load->model('wishlist_m');

		//
		// Get the product ID - First check if posted, if not use the direct product_id passed in
		//
		$product_id = $this->input->post('product_id') ? $this->input->post('product_id') : $product_id;


		if( $this->_wishlist_predelete( $this->current_user->id, $product_id ) )
		{
			if( $this->wishlist_m->remove($this->current_user->id, $product_id) )
			{

				$this->session->set_flashdata( JSONStatus::Success,  lang('nitrocart:my:wishlist:delete_success')  );
			}
		}
		else
		{
				$this->session->set_flashdata( JSONStatus::Error,  lang('nitrocart:my:wishlist:delete_error')  );
		}


		$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/my/wishlist';
		redirect($redirect);
	}




	/**
	 * Provides an ability to pre-check the product that is beeing requested to be removed from the wishlist
	 *
	 * There are not too many checks for delete, at least check the the ID are numeric.
	 * If we need to expand upon the checking, i.e in future we may want to warn the customer on specials of the product then we can put
	 * more checking codition here.
	 */
	private function _wishlist_predelete($product_id = 0)
	{

		//
		// Check validity of product ID
		//
		if ( (is_numeric($product_id)) && ($product_id <= 0) )
		{
			return false;
		}


		return true;
	}

	private function setLayoutForShop()
	{
		$preferred = 'nitrocart_my.html';
		$second = 'nitrocart.html';
		$layout = 'default.html';

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