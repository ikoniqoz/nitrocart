<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/core/cart_core.php');
class Cart extends Cart_core
{


	public function __construct()
	{
		parent::__construct();

		//requires checkout to be re-processed
		$this->cart_action();		

		// Retrieve some core settings
		$this->shop_title = Settings::get('shop_name');		//Get the shop name

		// Load required classes
		$this->load->model('nitrocart/products_front_m');
		$this->load->model('nitrocart/products_variances_m');


		$this->template
				->set_breadcrumb('Home', '/')
				->set_breadcrumb(Settings::get('shop_name'),'/'.NC_ROUTE);

	}

	/**
	 * Display Cart - The cart content is accessed via the plugin
	 */
	public function index()
	{

		$coupon = $this->mycart->coupon();
		$contents = $this->mycart->contents();
		$item_count = $this->mycart->total_items();
		$total = $this->mycart->total();

		$view_file = ($item_count>0)?'common/cart':'common/cart_no_items';

		$this->template
			->title(Settings::get('shop_name'), 'Cart')					
			->set_breadcrumb('Cart')	
			->set('coupon',$coupon)
			->set('contents',$contents)		
			->set('item_count', $item_count)	
			->set('total', $total)	
			->build($view_file);	
			//->build('common/cart');	
	}


	/**
	 * Redirect after success
	 *
	 * This function does many things.
	 *
	 * Process are executed in this order
	 *		1. Get Basic ID/QTY data from POST/REQUEST
	 *		2. Gets the variant record from DB
	 *		3. Check that variant is valid object
	 *		4. Get product from DB, or return fail code
	 *		5. Check that product is object
	 *		6. Do some pre-add to cart checks
	 *
	 *
	 *
	 *
	 *
	 * @param INT $pid
	 * @param INT $qty
	 *
	 * @access public
	 */
	public function add($variant_id = 0, $qty = 1)
	{


		// 1. Get the basic data
		if( $this->input->post('pid') )
		{
			$variant_id = intval($this->input->post('pid'));
			$qty = intval( $this->input->post('qty') ); //do not allow 0
			$qty = ($qty)?$qty:1;
		}


		// 2. Get the variant from System
		$variant = $this->products_variances_m->get($variant_id);



		// 3. Check variant as Object
		is_object($variant) OR $this->_message_handler( JSONStatus::Error ,  CartActionCode::VARIANT_ID_NOT_VALID ,true );



		// 4. Get product from Database (incl basic checks) return error code if fails
		$product = $this->fetchProduct( $variant->product_id );




		// 5. If object, we can contiue, if not exit to exception handler which dies or redirects
		is_object($product) OR $this->_message_handler( JSONStatus::Error ,  $product ,true );



   		// Options are handled by extension modules
   		// we still need to init the array for the cart
		//$options = array();

		$data = new ViewObject();




		// Prepare the item for the cart
		// Product is now an array
		$data->product = nc_prepare_cart_item( $product , $variant ,  $qty , array() );





		//Add to cart - signal before and post add event
		Events::trigger('SHOPEVT_BeforeCartItemAdded', $data );




		$status = $this->mycart->insert( $data->product );



		//call local event
		$this->cart_crud();

	


		Events::trigger('SHOPEVT_CartItemAdded', array('item'=> $data->product, 'status' => $status) );




		$message_code = ($status)? CartActionCode::ITEM_ADD_SUCCESS : CartActionCode::ITEM_ADD_FAILED ;




		// true to redirect or json_die
		$this->_message_handler( ($status)? JSONStatus::Success : JSONStatus::Error , $message_code , true, $data->product['name']);

	}


	/**
	 * update()
	 *
	 *
	 * @access public
	 */
	public function update()
	{

		$thepost = $this->input->post();
		$data = new ViewObject();
		$data->update_data = [];

		unset( $thepost['update_cart'] );

		//prepare items for the update
		foreach ($thepost as $d_item)
		{
			//perhaps we should fetch the variant here ?
			$update_item = [];
			$update_item['rowid'] = $d_item['rowid'];
			$update_item['id'] = $d_item['id'];
			$update_item['qty'] = $d_item['qty'];
			$update_item['new_qty'] = $d_item['qty']; //in this case new_qty is qty

			//$update_item['base'] = 10; //gets changed in  discounts
			//$update_item['price'] = 10; //price gets changed in discounts module
			$data->update_data[] = $update_item;
		}

		//Check with other modules
		//we send the array, any discount module alter the prices as needed, the nitrocart/ci->cart will do the calcs
		Events::trigger('SHOPEVT_BeforeCartUpdate', $data );

		//apply possible qty changes and dletes
		$result = $this->mycart->update($data->update_data);

		//call local event
		$this->cart_crud();		

		parent::_message_handler( JSONStatus::Success , CartActionCode::CART_UPDATED , true);
	}


	/**
	 * 
	 */
	public function remove($rowid)
	{
		$this->delete($rowid);
	}


	/**
	 * Delete an item from the cart
	 *
	 * @param String RowId of product in cart
	 * @access public
	 */
	public function delete($rowid)
	{

		$items = $this->mycart->contents();

		if($items[$rowid])
		{
			$this->mycart->remove($rowid);
			//call local event
			$this->cart_crud();
			
			parent::_message_handler( JSONStatus::Success, CartActionCode::ITEM_REMOVE_SUCCESS , true, $items[$rowid]['name'] );
		}
		else
		{
			//else - if true before, will not execute this as we passed the kill command to true
			parent::_message_handler( JSONStatus::Error,  CartActionCode::ITEM_REMOVE_FAILED , true, lang('nitrocart:cart:unknown') );
		}
	}


	/**
	 * Apply and clear coupons for the cart.
	 * Pass in blank to clear, or pass a striing literal to apply.
	 * If the code doesnt exist any pre-existing code is removed.
	 * 
	 * @param  string $coupon [description]
	 * @return [type]         [description]
	 */
	public function coupon( $coupon = '' )
	{
		//we need to check tat coupons are enabled
		if($this->db->table_exists('nct_coupons') AND system_installed('feature_coupons') )
		{
			//where we get the coupon from
			$coupon = ($this->input->post('coupon'))?$this->input->post('coupon'):$coupon;

			$this->load->model('nitrocart/coupons_m');
			if($coupon=='')
			{
				$this->mycart->clear_coupon();
				$this->_message_handler( JSONStatus::Success , CartActionCode::COUPON_REMOVED , true);		
			}
			// else
			if($coupon = $this->coupons_m->get_coupon($coupon))
			{
				if($this->mycart->apply_coupon( $coupon ) )
				{
					$this->_message_handler( JSONStatus::Success , CartActionCode::COUPON_VOUCHER_APPLIED , true);
				}	
			}
			$this->_message_handler( JSONStatus::Error , CartActionCode::COUPON_VOUCHER_NOT_APPLIED , true);
		}
		else
		{
			//what... we have no table.. possibly that the feature is not enabled
			$this->_message_handler( JSONStatus::Error , CartActionCode::COUPON_NOT_AVAILABLE , true);
		}
	}



	/**
	 * Empties the current cart : previously drop()
	 */
	public function clear()
	{
		$this->mycart->destroy();
		//call local event
		$this->cart_crud();
		$this->_message_handler( JSONStatus::Success , CartActionCode::CART_DESTROYED , true);
	}
	
}