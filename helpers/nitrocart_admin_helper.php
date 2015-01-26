<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/



/**
 * Return a users display name based on settings
 *
 * @param int $user the users id
 * @param string $linked if true a link to the profile page is returned, 
 *                       if false it returns just the display name.
 * @return  string
 */
function user_displaygroup($user)
{
    // User is numeric and user hasn't been pulled yet isn't set.
    if (is_numeric($user))
    {
        $user = ci()->ion_auth->get_user($user);
    }

    $user = (array) $user;
    return $user['group_description'];

}

if (!function_exists('make_UUID'))
{
    function make_UUID($input = '',$options=[])
    {
    	$braces = (isset($options['braces']))?$options['braces']:1;
    	$hyphen = (isset($options['hyphen']))?$options['hyphen']:1;

        $input = ($input=='')? uniqid(rand(), true) : $input ;
        
        mt_srand((double)microtime()*10000);

        $charid = strtoupper(md5($input));
        $hyphen = ($hyphen)?chr(45):'';
        $brace_start = ($braces)?chr(123):'';
        $brace_end = ($braces)?chr(125):'';
        $uuid = $brace_start.substr($charid, 0, 8).$hyphen.substr($charid, 8, 4).$hyphen.substr($charid,12, 4).$hyphen.substr($charid,16, 4).$hyphen.substr($charid,20,12).$brace_end;
        return $uuid;  
    }
}

if (!function_exists('Helper_order_paid_status_color'))
{
	function Helper_order_paid_status_color($status_key, $paid_date='x')
	{
		if($paid_date!='x')
		{
			$status_key = $paid_date!=NULL ? 'paid' : 'unpaid' ;
		}
		if($status_key == 'paid') return 'blue';
		return 'orange';
	}
}

/**
 * Determin the status color based on workflow percentage
 */
if (!function_exists('Helper_order_status_color'))
{
	function Helper_order_status_color($status_key)
	{
		$ci =& get_instance();
		if($result = $ci->db->where('id',$status_key)->get('nct_workflows')->row())
		{
			if($result->pcent < 25) return 'orange';
			if($result->pcent < 50) return 'green';
			if($result->pcent < 75) return 'blue';		
			if($result->pcent < 101) return 'red';		
		}

		//error
		return 'red';
	}
}
if (!function_exists('sort_module_tabs'))
{
	function sort_module_tabs($a, $b)
	{
		$_a_i = isset($a->prod_tab_order)?(int)$a->prod_tab_order:4;
		$_b_i = isset($b->prod_tab_order)?(int)$b->prod_tab_order:4;
	    return strcmp($_a_i, $_b_i);
	}
}

//if (!function_exists('helper_link'))



if (!function_exists('yesNoBOOL'))
{
	/** 
	 *  yesNoBOOL($_bool, 'string', 'Yes', 'No' )
	 *
	 *  yesNoBOOL($_NO_TEXT, 'bool', true, false )
	 *
	 * [yesNoBOOL description]
	 * @param  boolean $val1 true|false|yes|no
	 * @param  string  $get  string|bool
	 * @return [type]        return as requested as get |string|bool
	 */
	function yesNoBOOL($val = false, $get='string', $yes ='Yes', $no='No' )
	{
		if($get=='string')
		{
			return ($val)? $yes : $no ;
		}
		else
		{
			return ( strtolower($val) == 'yes' )? true : false ;
		}
		return 'no';
	}
}

/**
 *
 * Adopted from http://cubiq.org/the-perfect-php-clean-url-generator
 *
 * echo toAscii("Mess'd up --text-- just (to) stress /test/ ?our! `little` \\clean\\ url fun.ction!?-->");
 *
 *	echo toAscii("Custom`delimiter*example", array('*', '`'));
 *	returns: custom-delimiter-example
 *
 *	echo toAscii("Tänk efter nu – förr'n vi föser dig bort"); // Swedish
 *	returns: tank-efter-nu-forrn-vi-foser-dig-bort
 *
 *
 * @param unknown_type $str
 * @param unknown_type $replace
 * @param unknown_type $delimiter
 * @return mixed
 */
if (!function_exists('shop_slugify'))
{
	//make sure the slug is valid
	function shop_slugify($slug, $replace=array(), $delimiter='-')
	{
		setlocale(LC_ALL, 'en_US.UTF8');
		if ( !empty($replace) )
		{
			$slug = str_replace((array)$replace, ' ', $slug);
		}
		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		return $clean;
	}
}


if (!function_exists('nc_get_zone_name'))
{
	//make sure the slug is valid
	function nc_get_zone_name( $zone_id , $as_link=false)
	{
		$ci =& get_instance();
		$ci->load->model('nitrocart/zones_m');
		if($zone = $ci->zones_m->get($zone_id))
		{
			return $zone->name;
		}
		return 'Default MCL (Master Countries List)';
	}

}

if (!function_exists('nc_product_type_name'))
{
	function nc_product_type_name($type_id = 0)
	{
		$ci =& get_instance();
		$ci->load->model('nitrocart/products_types_m');
		$type = $ci->products_types_m->get($type_id);
		if(!$type) 
		{
			return '<span style="color:red">Not found</span>';
		}

		return $type->name;

	}
}
if (!function_exists('nc_product_name'))
{
	function nc_product_name($product_id = 0)
	{
		$ci =& get_instance();
		$ci->load->model('nitrocart/admin/products_admin_m');
		$product = $ci->products_admin_m->get($product_id);
		if(!$product) return 'Unknown';
		return $product->name; //although we store the id of the3 variant type, we have a ref to the name too!
		//return nc_variant_name_bytype($variant->type_id);
	}
}

if (!function_exists('nc_get_package_group_name'))
{
	function nc_get_package_group_name($group_id = 0)
	{
		$ci =& get_instance();
		$ci->load->model('nitrocart/packages_groups_m');
		$pkg_group = $ci->packages_groups_m->get($group_id);
		if(!$pkg_group) return 'Unknown';
		return $pkg_group->name; //although we store the id of the3 variant type, we have a ref to the name too!
		//return nc_variant_name_bytype($variant->type_id);
	}
}

/**
 * Simulate a cart item : 
 */
if (!function_exists('nc_sim_cart_item'))
{
    function nc_sim_cart_item( $variance, $qty = 1 )
    {

		$ci =& get_instance();
		$ci->load->model('nitrocart/admin/products_admin_m');

    	$product = $ci->products_admin_m->get($variance->product_id);
    	if($product)
    	{
    		$product->variant  = $variance;
    	}
    	else
    	{
    		return false;
    	}

		// Assign the Cart item array
		$data = array(
				'id' => $product->variant->id,
				'productid' => $product->id,
				'tax_id' 	=> $product->tax_id,		
				'variance' 	=> $product->variant->id,
				'pkg_group_id' => $product->variant->pkg_group_id,
				'discountable' => $product->variant->discountable,
				'slug' 		=> $product->slug,
				'qty' 		=> $qty,
				'new_qty' 	=> $qty, 	
				'price' 	=> $product->variant->price,	
				'list_price' => $product->variant->price, 
				'discount' 	=> 0, 					
				'base' 		=> $product->variant->base,
				'name' 		=> convert_accented_characters( $product->name ),
				'options' 	=> array(),
				'discount_message' => '',
				'height' 	=> $product->variant->height, 	
				'width' 	=> $product->variant->width,
				'length' 	=> $product->variant->length,
				'weight' 	=> $product->variant->weight,

		);

		return $data;
    }

}