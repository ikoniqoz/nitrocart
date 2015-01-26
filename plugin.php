<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

class Plugin_Nitrocart extends Plugin
{
	public $version = '1.0.1';

	public $name = [
		'en' => 'NitroCart',
	];

	public $description = [
		'en' => 'Access user and cart information for almost any part of NitroCart.',
	];

    public function __construct()
    {             
            
    }

	/**
	 * Get the CI instance into this object
	 *
	 * @param unknown_type $var
	 */
	public function __get($var)
	{
		if (isset(get_instance()->$var))
		{
			return get_instance()->$var;
		}
	}


	/**
	 * @deprecated @see products plugin
	 * @return [type] [description]
	 *
	 * Returns a PluginDoc array that PyroCMS uses
	 * to build the reference in the admin panel
	 *
	 * All options are listed here but refer
	 * to the Asset plugin for a larger example
	 *
	 * @return array
	 */
	public function _self_doc()
	{
		$info = [
			'cart' => [
				'description' => [
					'en' => 'Display the cart contents.'
				],
				'single' => false,
				'double' => true,
				'variables' => 'id|rowid|name|qty|price|subtotal',
				'attributes' => [],
			],
			'currency' => [],
				'description' => [
					'en' => 'Display the NitroCart default currency symbol OR format a float value to our currency format.'
				],
				'single' => true,
				'double' => false,
				'variables' => 'id|rowid|name|qty|price|subtotal',
				'attributes' => [
						'format' => [
							'type' => 'float',
							'required' => false,
						],
				],
			];
		
			return $info;
	}


	/**
	 * @deprecated @see products plugin
	 * @return [type] [description]
	 */
	function branding()
	{	
        $xParam 		= $this->attribute('x', '');
        $class 			= $this->attribute('class', '');        
		$x_array 		= explode ( ',' , $xParam);
		return "Powered by <a class='{$class}' target='_new' href='http://nitrocart.net'>NitroCart</a>";
	}

	/**
	 * {{nitrocart:has feature='coupons'}}
	 */
	function has()
	{	
        $feature 		= $this->attribute('feature', 'false');
        $extension 		= $this->attribute('extension', 'false');
        $subsystem 		= $this->attribute('subsystem', 'false');
        $debug 		= $this->attribute('debug', 'no');

         
		$this->load->helper('nitrocart/nitrocart');

		$options = [];

		if($feature != 'false')
		{	
			$installed = system_installed('feature_'.$feature);
        	return ['value'=>$installed];
    	}
		if($extension != 'false')
		{	
			$installed = nc_module_installed($extension);
        	return  ['value'=>$installed];
    	} 
		if($subsystem != 'false')
		{	
			$installed = system_installed('system_'.$feature);
        	return  ['value'=>$installed];
    	}    	

        if($debug=='yes')
        {
        	//return $this->content();
			//var_dump($this->content(),$this['value'=>true]);die;
		}
		//var_dump($this->parser->parse($this->content(), $options));die;


    	//default
    	return ['value'=>false];   	
	}

	/**
	 * Basic cart plugin
	 */
	function cart()
	{
 		//$this->load->library('nitrocart/nitrocore_library');  

 		$show_max 		= (int) $this->attribute('show_max', '0');

 		//load just in case of non shop page 
		$this->load->helper('nitrocart/nitrocart');
		$this->load->library('nitrocart/mycart');  

		$c = $this->mycart->contents();

		if($show_max === 0)
		{
			$show_max = false;
		}
		else
		{
			$show_max = (int) $show_max;
		}

		$count = 0;
		$c = ($c)?$c:[];
		$out_array = [];
		foreach($c as $key => $citem)
		{
			$c[$key]['price'] = nc_format_price($citem['price']);
			$c[$key]['subtotal'] = nc_format_price($citem['subtotal']);
			$c[$key]['base'] = nc_format_price($citem['base']);
			if(($show_max > $count) OR ($show_max == false))
			{ 
				$out_array[] = $c[$key];
			} 
			$count++;
		}

		$ret_array =[];
		$ret_array[]=[
					'item_count'=> $this->mycart->total_items(),
					'contents'=> $out_array,
					'total'=>  nc_format_price($this->mycart->total()),
				];

		return $ret_array;
	}
	
	/**
	 * @return [type] [description]
	 *
     * {{nitrocart:expresscheckout}}
     *
     * {{nitrocart:expresscheckout text='foo bar' class='btn' x=''}}
     *
     * All params are optional
     *
     *  1. Has at least 1 item in cart
     *  2. Has Billing address in system (and a registered user)
     *  3. has Shipping address in system (and a registered user)
     *  4. System has at least 1 shipment method
     *  5. system has at least 1 gateway method
     * @return [type] [description]
     */
	function expresscheckout()
	{
        if(!$this->current_user)
        	return '';

        $xParam 		= $this->attribute('x', '');
        $link_class 	= $this->attribute('class', '');
        $link_text 		= $this->attribute('text', 'Express Checkout');

		$x_array 		= explode ( ',' , $xParam);
		$fallback  		= (in_array( "FALLBACK" , $x_array )) ? true: false;

		$fallback_link 	= "<a class='{$link_class}' href='{{url:site}}". NC_ROUTE ."/checkout/'>Checkout</a>";
		$express_link	= "<a class='{$link_class}' href='{{url:site}}". NC_ROUTE ."/checkout/express'>{$link_text}</a>";
		$na 			= '';

		//return the e/co link or a fallback, otherwise blank
		return ( nc_can_express_checkout() ) ? $express_link : (($fallback) ? $fallback_link : $na) ;
	}

	/**
	 * @return [type] [description]
	 */
	function can_expresscheckout()
	{
        if(!$this->current_user)
        	return false;

		return ( nc_can_express_checkout() ) ? true : false ;
	}


	
	/**
	 * Currency symbol may be deprecated in future. Unsure.
	 * @deprecated
	 *
	 * @return [type] [description]
	 */
	public function settings()
	{
		//$product_id = $this->attribute('id', NULL);
		$this->load->library('nitrocart/currency_library');

		$ret_array[] = array(
				'allow_guest'=> Settings::get('shop_allow_guest_checkout'),
				'name'=>Settings::get('shop_name'),
				'open'=>Settings::get('shop_open_status'),
				'curr_symbol'=> $this->currency_library->getCurrencySymbol(),
				'is_store' => (Settings::get('shop_store_type')=='standard')?true:false,
			);

		return $ret_array;
	}

	/**
	 * For now we only retrieve the symbol, but we should add options for 2 letter code, etc..
	 * @return [type] [description]
	 * @deprecated
	 *
	 * {{ nitrocart:currency }} - return $ L or pound
	 * {{ nitrocart:currency format="{{total}}" }} - returns the price submited with the currency
	 */
	function currency()
	{
		$this->load->helper('nitrocart/nitrocart');
		$option = $this->attribute( 'get' , 'symbol' );
		$format = $this->attribute( 'format' , 'NO' );

		if($format == "NO")
		{
			//then we just need the symbol
			$this->load->library('nitrocart/currency_library');
			return $this->currency_library->getCurrencySymbol();
		}

		return nc_format_price($format);
	}



}
/* End of file plugin.php */