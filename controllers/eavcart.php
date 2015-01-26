<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(  dirname(__FILE__) . '/cart.php');
class Eavcart extends Cart
{


	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();	
	}


	/**
	 * Interface only:the traditional way, product_id is actually variance_id
	 */
	public function add($product_id = 0, $qty = 1, $cartmode='standard')
	{

		//get any post values - will be used to determine variance
		$input = $this->input->post();


		//var_dump($input);die;

		//get cart mode
		if(isset($input['eavcartmode']))
		{
			$cartmode = $input['eavcartmode'];
		}

		//get product id
		if(isset($input['form_eav_product_id']))
		{
			$product_id = (int) $input['form_eav_product_id'];

			//remove this key
			unset($input['form_eav_product_id']);

		}


		//get product id
		if(isset($input['pid']))
		{
			unset($input['pid']);
		}
		

		//get product id
		if(isset($input['qty']))
		{
			$input['qty'] = (int) $input['qty'];
			$input['qty'] = ($qty > $input['qty'])?$qty:$input['qty'];
			$qty = $input['qty'];
		}


		//determine action
		switch($cartmode)
		{
			case 'eav':			
				$this->_eav_add($input, $product_id, $qty);	
				break;
			case 'standard':
			default:
				parent::add($product_id, $qty);
				break;				
		}
	}


	/**
	 * Search for the variation then add to cart using eav model
	 */
	private function _eav_add($input, $product_id = 0, $qty = 1)
	{

		//
		// Load custom libraries
		//
		$this->load->library('nitrocart/Toolbox/Nc_string'); 


		//
		// Do a pre check to see if there are attributes, if not, just add default first
		//
		if($noattributes = $this->pre_check($product_id))
		{
			$this->addx( $noattributes , $qty );
		}



		//
		// Builds the list of option ID data
		//
		$options = $this->_seed_plantation($input);




		//
		// Prune - remove anomolies
		// Harvest - Collect info on the options
		//
		$nceavarray = $this->_prune_and_harvest($product_id, $options);

		


		//
		// If we dont have any, it will fail the test
		//
		if( $variant = $this->_pick( $nceavarray ) )
		{
			// add to cart
			$this->addx( $variant , $qty );

			return;		
		}	
		

		//
		// We only get to here if the product could not be found
		//
		//echo "No matching product or variation with those details, try different options.";die;
		$this->_message_handler( JSONStatus::Error , CartActionCode::EAV_VARIANT_NOT_FOUND , true);
	}




	/**
	 * we must pre-check to see whether the product even has any attributes.
	 * If there are no attributes all the variations are essentially different stock locations.
	 * however the product will be considered the same. And same SKU. So lets just get the first of the mark.
	 */
	private function pre_check($product_id)
	{

		if($this->_has_attributes($product_id)==false)
		{
			return $this->db->where('product_id',$product_id)->where('deleted',NULL)->where('available',1)->get('nct_products_variances')->row();
		}

		return false;
	}




	private function _has_attributes($product_id)
	{

		if($this->db->where('e_product',$product_id)->where('e_variance',NULL)->get('nct_e_attributes')->row())
		{
			return true;
		}

		return false;
	}


	/**
	 * Build the list of ID's for the options requested.
	 * We will use this data to filter the variations down to 
	 * the customers expectations.
	 *
	 *
	 */
	private function _seed_plantation($input)
	{

		$options = [];

		foreach($input as $key=>$value)
		{
			$string = new NCString($key);
			if($string->startsWith('form_eav_'))
			{
				if($string->rightOf('form_eav_')->isNumeric())
				{
					if($intval = $string->rightOf('form_eav_')->toNCFloat()->toInt())
					{
						$options[$intval] = $value;
					}
				}
			}
		}
		return $options;
	}




	/**
	 * Combines both prune and harvest into a single function
	 *
	 *
	 * Lets make sure that the set of requested 
	 * options are all from the same stem.
	 *
	 * What we need to do is remove anything from other
	 * products
	 *
	 */
	private function _prune_and_harvest( $product_id, $options )
	{
		$_stem = NULL;

		$array = new NCEAVArray();


		// Get all the variances in a NCEAVArray
		$rows = $this->db->where('e_variance IS NOT NULL', null, false)->where('e_product', $product_id)->get('nct_e_attributes')->result();
		//var_dump($rows);die;

		// Build te NCAray and trim nulls
		$array = $array->pushall($rows)->noNullValues();



		$new = [];
		foreach($options as $id=>$value)
		{
			$row = $this->db->where('id',$id)->get('nct_e_attributes')->row();
			$new[$row->e_label] = $value;
		}


		foreach($new as $key=>$value)
		{
			$array = $array->variancesWith($key,$value);
		}

	
		return $array;
	}	




	/**
	 * Pick will select the individual SKU/Product variant that...
	 * 1, matches the user request, 
	 * 2, is available for picking (availability ect...)
	 */
	private function _pick( $nceavarray )
	{

		// All variances that remain are matching of the users request.
		// so we need to see which variances are valid by the admin/office
		// lets get all the variances that remain..

		$variants = [];
		foreach( $nceavarray->iterator()  as $i )
		{
			$variants[$i->getVarianceID()] = $i->getVarianceID();
		}

		// Dont just get the first of the same attributes.
		// Actually get the one with the lowest price as the user sees this first
		$possibilities = [];
		foreach($variants as $variant)
		{
			if($prodvar = $this->db
				->where('id',$variant)
				->where('deleted',NULL)
				->where('available',1)
				->get('nct_products_variances')->row())
			{
				$possibilities[$variant] = $variant;
			}
		}

		if(!count($possibilities))
		{
			return false;
		}


		if($prodvar = $this->db->where_in('id',$possibilities)->order_by('price','asc')->get('nct_products_variances')->row())
		{
			return $prodvar;
		}

		//out of luck.. no results found
		return false;
	}


	/**
	 * Add the variant to the cart, this is a cut back version off add() from the parent
	 * As we have already undergone some validation not all the validation was nessessary
	 */
	private function addx($variant, $qty = 1)
	{


		// 1. Get product from Database (incl basic checks) return error code if fails
		$product = $this->fetchProduct( $variant->product_id );




		// 2. If object, we can contiue, if not exit to exception handler which dies or redirects
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




		//
		// Add the product to the cart
		//
		$status = $this->mycart->insert( $data->product );



		// Call local event, this does a whole bunch of stuff like checking for perm cart etc..
		$this->cart_crud();

	

		// Nootify anyone..
		Events::trigger('SHOPEVT_CartItemAdded', array('item'=> $data->product, 'status' => $status) );



		// Based on the action status let the user know whats ging on..
		$message_code = ($status)? CartActionCode::ITEM_ADD_SUCCESS : CartActionCode::ITEM_ADD_FAILED ;




		// true to redirect or json_die
		$this->_message_handler( ($status)? JSONStatus::Success : JSONStatus::Error , $message_code , true, $data->product['name']);
	}
	
}

