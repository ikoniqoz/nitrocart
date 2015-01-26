<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Cart_core extends Public_Controller
{

	// List of messages (error|success) for return
	protected $_MESSAGES = [];
	protected $_sys_message = [];


	public function __construct()
	{
		parent::__construct();

		
		Events::trigger('SHOPEVT_ShopPublicController');


		$this->load->library('nitrocart/Toolbox/Nc_enums');

		$this->initMessages();
		$this->initMessageObject();
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/cart';


		Settings::get('shop_open_status') OR redirect( NC_ROUTE.'/closed');
		$this->shop_type = Settings::get('shop_store_type');		
		if($this->shop_type =='showcase')
		{
			$this->session->set_flashdata( JSONStatus::Error , 'There is no checkout facility on this store.' );
			redirect( NC_ROUTE );
		}	
	}


	/**
	 * Any cart crud option. Not just a request but a successfull request
	 * @return [type] [description]
	 */
	protected function cart_crud()
	{
		if( system_installed('feature_permcart') )
		{
			if($this->current_user)
			{
				$this->load->model('nitrocart/carts_m');
				$this->carts_m->clear_all();
				foreach($this->mycart->contents() as $item)
				{
					$this->carts_m->modify($this->current_user->id, $item['id'] , $item['productid'], $item['price'], $item['qty'], $item['options'] );
				}
			}
		}
	}


	/**
	 * Redirect after success
	 *
	 * @param INT $id
	 * @param INT $qty
	 *
	 * @access public
	 */
	protected function fetchProduct($id = 0)
	{

		// Get product from DB
		//$_product = $this->products_front_m->get($id,'id');
		$_product = $this->products_front_m->get($id);


		// Check if product exist or still available
		if(!$_product)
		{
			return CartActionCode::ITEM_NOT_FOUND;
		}

		if( $_product->deleted != NULL )
		{
			return CartActionCode::ITEM_NOT_AVAILABLE;
		}

		if( $_product->public == ProductVisibility::Invisible )
		{
			return CartActionCode::ITEM_INVISIBLE;
		}

		// If we have reached this point then we can validate successfully
		return $_product;
	}

	/**
	 * @access private
	 */
	protected function _check_inventory($_product, $qty = 1)
	{
		return true;
	}



	// If '' is passed as string format, no formatting
	//if anything else is passed
	//kill is ajax
	protected function _message_handler( $status='error', $error_code=0 , $kill=true, $string_format = '')
	{
		$this->_sys_message['status'] = $status;
		$this->_sys_message['message'] = ($string_format!='')?sprintf($this->_MESSAGES[$error_code],$string_format):$this->_MESSAGES[$error_code];
		$this->_sys_message['qty'] = $this->mycart->total_items();
		$this->_sys_message['cost'] = number_format( (float) $this->mycart->total() , 2); //cost of cart

		if(! $this->input->is_ajax_request())
		{
			$this->session->set_flashdata( $this->_sys_message['status'] , $this->_sys_message['message'] );
		}

		if($kill)
			$this->sendResponse();

	}

	protected function sendResponse()
	{
		if($this->input->is_ajax_request())
		{
			echo json_encode($this->_sys_message);exit;
		}
		else
		{
			redirect($this->refer);
		}
	}



	protected function initMessages()
	{
		$this->_MESSAGES[CartActionCode::CART_DESTROYED] 		= lang('nitrocart:cart:dropped');
		$this->_MESSAGES[CartActionCode::CART_UPDATED] 			= lang('nitrocart:common:cart_updated');
		$this->_MESSAGES[CartActionCode::ITEM_ADD_SUCCESS] 		= lang('nitrocart:cart:item_added');
		$this->_MESSAGES[CartActionCode::ITEM_ADD_FAILED] 		= lang('nitrocart:cart:item_not_added');
		$this->_MESSAGES[CartActionCode::ITEM_NOT_FOUND] 		= lang('nitrocart:cart:product_not_found');
		$this->_MESSAGES[CartActionCode::ITEM_NOT_AVAILABLE] 	= lang('nitrocart:cart:product_not_available');
		$this->_MESSAGES[CartActionCode::ITEM_INVISIBLE] 		= lang('nitrocart:cart:product_not_available') . " - #2";
		$this->_MESSAGES[CartActionCode::USER_MUST_LOGIN] 		= lang('nitrocart:cart:you_must_login_before_shopping');

		$this->_MESSAGES[CartActionCode::ITEM_REMOVE_SUCCESS] 	= lang('nitrocart:cart:item_removed');
		$this->_MESSAGES[CartActionCode::ITEM_NOT_IN_CART] 		= lang('nitrocart:cart:not_in_cart');


		$this->_MESSAGES[CartActionCode::VARIANT_ID_NOT_VALID] 		= lang('nitrocart:cart:variant_invalid');
		$this->_MESSAGES[CartActionCode::VARIANT_ID_NOT_AVAILABLE] 	= lang('nitrocart:cart:variant_invalid'). ' - #2';
		$this->_MESSAGES[CartActionCode::EAV_VARIANT_NOT_FOUND] 	= "No matching product or variation with those details, try different options.";


		$this->_MESSAGES[CartActionCode::COUPON_VOUCHER_APPLIED] 		= 'Coupon applied.';
		$this->_MESSAGES[CartActionCode::COUPON_VOUCHER_NOT_APPLIED] 	= 'Coupon not found.';
		$this->_MESSAGES[CartActionCode::COUPON_REMOVED] 				= 'Coupon cleared...';
		$this->_MESSAGES[CartActionCode::COUPON_NOT_AVAILABLE] 			= 'Coupon feature is not available at this time...';
	}


	protected function initMessageObject()
	{
		$this->_sys_message = [];
		$this->_sys_message['status'] = JSONStatus::Error;
		$this->_sys_message['message'] = 'Unknown';
		$this->_sys_message['cost'] = 0.00;
		$this->_sys_message['qty'] = 0;
	}


	protected function cart_action()
	{
		$this->session->set_userdata('total_items_require_shipping', 0 ); 
        $this->session->set_userdata('previous_step',0);
        $this->session->set_userdata('current_step',0);
	}
}



final class CartActionCode
{
	// [100-199] Cart actions
	const CART_DESTROYED 			= 101;
	const CART_UPDATED 				= 102;

	//[200-299] Item-Cart actions
	const ITEM_ADD_FAILED			= 201;
	const ITEM_ADD_SUCCESS			= 205;
	const ITEM_REMOVE_FAILED		= 211;
	const ITEM_REMOVE_SUCCESS		= 212;
	const ITEM_NOT_FOUND 			= 221;
	const ITEM_NOT_IN_CART			= 231;
	const ITEM_NOT_AVAILABLE		= 241;
	const ITEM_INVISIBLE			= 251; //product is invisible to public, not available



	//[300-399] Variant Validation
	const VARIANT_ID_NOT_VALID 		= 301;
	const VARIANT_ID_NOT_AVAILABLE 	= 302;

	//Variant validation on the specific EAV cart 
	const EAV_VARIANT_NOT_FOUND 	= 351;


	//[400-499] User status messages
	const USER_MUST_LOGIN 			= 401;


	const COUPON_VOUCHER_APPLIED 		= 500;
	const COUPON_VOUCHER_NOT_APPLIED 	= 501;
	const COUPON_REMOVED 				= 502;	
	const COUPON_NOT_AVAILABLE			= 503;

}