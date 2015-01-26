<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
if (!function_exists('nc_can_express_checkout'))
{
	function nc_can_express_checkout($return_data=false)
	{
        $canExpress = false;

		$CI =& get_instance();
		$CI->load->library('nitrocart/mycart');

        if(!$CI->current_user)
        	return false;

        $ccount = $CI->mycart->total_items();
        $billing_address = $CI->db->where('billing',1)->where('deleted',NULL)->where('user_id', $CI->current_user->id)->limit(1)->get('nct_addresses')->row();
        $shipping_address = $CI->db->where('shipping',1)->where('deleted',NULL)->where('user_id', $CI->current_user->id)->limit(1)->get('nct_addresses')->row();
        $gateway 	= $CI->db->where('module_type','gateway')->where('enabled','1')->limit(1)->get('nct_checkout_options')->row();
		$shipments 	= $CI->db->where('module_type','shipping')->where('enabled','1')->limit(1)->get('nct_checkout_options')->row();

        $canExpress = ( ($ccount >0) AND $billing_address AND $shipping_address AND $gateway AND $shipments ) ? true : false ;

        return $canExpress;
    }

}



if (!function_exists('nc_cart_contents'))
{
	function nc_cart_contents()
	{
		$ci =& get_instance();
		$ci->load->library('nitrocart/mycart');
		$c = $ci->mycart->contents();

		foreach($c as $key => $citem)
		{
			$c[$key]['price'] = nc_format_price($citem['price']);
			$c[$key]['subtotal'] = nc_format_price($citem['subtotal']);
			$c[$key]['base'] = nc_format_price($citem['base']);
		}

		return $c;

	}
}

if (!function_exists('nc_prepare_cart_item'))
{
	function nc_prepare_cart_item($product, $variant,  $qty=1, $options=array())
	{
		$ci =& get_instance();		

		// Clean name from bad characters
		$name =  convert_accented_characters( $product->name );


		// Gets the new qty (precheck) - this assist any discount module knowing how to
		// Discount based om the total of this variant/product
		$curr_qty = $ci->mycart->productv_qty($variant->id, 'id');



		// Assign the Cart item array
		$data = [
					'id' 			=> $variant->id,
					'productid' 	=> $product->id,
					'tax_id' 		=> $product->tax_id,		//meta data used to calc totals later
					'variance' 		=> $variant->id,
					'pkg_group_id' 	=> $variant->pkg_group_id,
					'discountable' 	=> $variant->discountable,
					'points' 		=> $product->points,
					'slug' 			=> $product->slug,
					'qty' 			=> $qty,
					'new_qty' 		=> ($curr_qty + $qty), 					// here we want to add the qty requested, with any qty in the current cart, this will be used for calc discounts on the event fire
					'price' 		=> number_format($variant->price,2), 	// the price listed on the site - any discounts based on membership ?!
					'list_price' 	=> number_format($variant->price,2), 	// the price listed on the site
					'discount' 		=> 0, 									// any discounts applied o this product (this is just meta data)
					'base' 			=> number_format($variant->base,2),
					'name' 			=> $name,
					'variant_name' 	=> $variant->name,
					'options' 		=> $options,
					'discount_message' => '',
					'height' 		=> $variant->height, 		
					'width' 		=> $variant->width,
					'length' 		=> $variant->length,
					'weight' 		=> $variant->weight,
					'is_shippable'	=> $variant->is_shippable,
					'is_digital'	=> $variant->is_digital,
				];

		return $data;
	}
}





	

/**
 *
 * @return The Country object[data] from the db by passing the 2 letter code or country id
 * @param by = code2 | id
 * @param id = the 2 letter country code or Country ID
 *
 *
 */
if (!function_exists('nc_country'))
{
	function nc_country($id, $by='code2' )
	{
		//format the input by param
		$id = ($by=='code2') ? strtoupper($id):(int) $id;
		$ci =& get_instance();
        if($ci->db->table_exists('nct_countries'))
        {
            $row = $ci->db->where($by, $id )->get('nct_countries')->row();
            if($row)
            {
            	return $row;
            }
        }
		return false;
	}
}

/**
 * Get the country name by its 2 letter code, otherwise return the code
 */
if (!function_exists('nc_country_name'))
{
	function nc_country_name($country_code)
	{
		$country = nc_country($country_code,'code2');
		return ($country) ? $country->name : $country_code ;
	}
}


/**
 * check to see if a system is installed.
 * We need this both front and backend to see if users have access to variou sections
 */
if (!function_exists('system_installed'))
{
	function system_installed($subsystem_name)
	{
		$is_installed = false;
		$ci =& get_instance();
        if($ci->db->table_exists('nct_systems'))
        {
            $row = $ci->db->where('driver', strtolower($subsystem_name) )->get('nct_systems')->row();
            if($row)
            {
            	if($row->installed == 1)
            		return true;
            }
        }
		return false;
	}
}
if (!function_exists('nc_module_installed'))
{
	function nc_module_installed($namespace)
	{
		$is_installed = false;
		$ci =& get_instance();
        if($ci->db->table_exists('nct_modules'))
        {
            $row = $ci->db->where('namespace',$namespace)->get('nct_modules')->row();
            if($row)
            {
            	//if($row->installed == 1)
            		return true;
            }
        }
		return false;
	}
}

if (!function_exists('system_installed_or_die'))
{
	function system_installed_or_die($subsystem_name , $redirect_to='admin')
	{
		$ci =& get_instance();

		if( ! system_installed($subsystem_name) )
		{
			$_message = 'Access denied';
			if ($ci->input->is_ajax_request())
			{
				echo json_encode(array('error' => $_message) );die;
			}
			$ci->session->set_flashdata('error', $_message );
			redirect($redirect_to);
		}
		return true;
	}
}

/*date('d / M / Y ', $order->order_date)*/
if (!function_exists('nc_format_date'))
{
	function nc_format_date($date,$in_format='timestamp')
	{
		$formats = array(0 =>"d-m-Y",1=>"d/m/Y",2=>"m-d-Y",3=>"m/d/Y");
		$format = Settings::get('shop_date_format');

		if($in_format=='timestamp') return date($formats[$format],$date);

		$date = new DateTime($date);
		return $date->format($formats[$format]);
	}
}




/**
 * @deprecated price or currency symbol should be 
 * displayed autmatically in the price
 */
if (!function_exists('nc_currency_symbol'))
{
	function nc_currency_symbol()
	{
		$ci =& get_instance();
		$ci->load->library('nitrocart/currency_library');
		return $ci->currency_library->getCurrencySymbol();
	}
}

/**
 * @deprecated - This should not be used anymore
 */
if (!function_exists('nc_format_price'))
{
	function nc_format_price($price_value)
	{
		// if we dont have route defined then we have a problem.
		//however we may be on another non-store page :(
		//if(!defined('NC_ROUTE')) return $price_value;
		$ci =& get_instance();
		$ci->load->library('nitrocart/currency_library');
		return $ci->currency_library->format($price_value);
	}
}

/**
 * Gets the product cover image by a given product id.
 * This first checks to see if the relevant table is installed and returns data
 * Additionally, a option can be set to return as a HTML IMG tag.
 *
 *
 * @param $product_id 	INT
 * @param $as_html 		BOOL
 */
if (!function_exists('nc_product_cover'))
{
	function nc_product_cover( $product_id = -1, $as_html = false, $tag_id='prod_cover' )
	{
		$img = NULL;
		$ci =& get_instance();
		if($ci->db->table_exists('nct_product_gallery'))
		{
			$img = $ci->db->where('cover',1)->where('product_id',$product_id)->limit(1)->get('nct_product_gallery')->row();
			$src = ($img)? $img->src : '/' ;

			if($as_html)
			{
				return "<img src='{$src}' id='{$tag_id}' height='100'>"  ;
			}		
		}
		return ($img) ? $img : NULL ;
	}
}


/*
 * Get variant name by ID of the Product Variantion
 * @deprecated. Variations will soon just become DB price records.
 * When options kickin we wont need variant names anymore
 */
if (!function_exists('nc_variant_name'))
{
	function nc_variant_name($product_variant = 0)
	{
		$ci =& get_instance();
		$ci->load->model('nitrocart/products_variances_m');
		$variant = $ci->products_variances_m->get($product_variant);
		if(!$variant) return 'Standard';
		return $variant->name; 

	}
}