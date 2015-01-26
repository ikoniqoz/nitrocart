<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Products_library extends ViewObject
{

	public function __construct($params = [])
	{
		parent::__construct();
		$this->load->library('settings/settings');
		log_message('debug', "Products Library Class Initialized");
	}

	public function plugin_variant($variant,$format)
	{
		$this->load->library('nitrocart/currency_library');

		$cs = $this->currency_library->getCurrencySymbol();
	   	$name 		= $variant['variant_text'];
    	$price 		= $cs.''.$variant['variant_price'];
    	$id 		= $variant['variant_id'];
    	$base 		= $cs.''.$variant['variant_base'];
    	$rrp 		= $cs.''.$variant['variant_rrp'];
    	$orprice 	= $variant['variant_original_price'];


    	$format = str_replace ( '{id}'  	, $id , $format );
    	$format = str_replace ( '{name}'  	, $name , $format );
		$format = str_replace ( '{price}' 	, $price , $format );
		$format = str_replace ( '{base}' 	, $base , $format );		
		$format = str_replace ( '{orprice}' , $orprice , $format );

		if($variant['variant_price'] < $variant['variant_original_price'])
		{
			$price_before_after = 'was '.$orprice.' now '.$price;
		}
		else
		{
			$price_before_after = 'now '.$price;
		}
		$format = str_replace ( '{price_before_after}' , $price_before_after , $format );



		if($variant['variant_price'] < $variant['variant_rrp'])
		{
			$price_rrp_special = 'Our price '.$price.' RRP '.$rrp;
		}
		else
		{
			$price_rrp_special = 'Our price '.$price;
		}	
		$format = str_replace ( '{price_rrp_special}' , $price_rrp_special , $format );

	
		return $format;
	}


	public function draw_radio($pre_html='',$variances_array=[],$format)
	{
		$default_selected=false;
		$html =  $pre_html;
        foreach( $variances_array as $vari)
        {	
			$out_format = $this->plugin_variant($vari,$format);
        	$_checked_text = '';
        	if($default_selected==false)
        	{
				$_checked_text = 'checked';
				$default_selected = true;
        	}
        	$html .= "<input type='radio' name='pid' value='".$vari['variant_id']."'  {$_checked_text}>".$out_format."<br />";
        }
       return $html;
	}
	public function draw_select($pre_html='',$variances_array=[],$format)
	{
		$html = $pre_html;
		$html .=  '<select name="pid">';		
        foreach($variances_array as $vari)
        {
        	$out_format = $this->products_library->plugin_variant($vari,$format);
        	$html .= '<option value="'.$vari['variant_id'].'">'.$out_format.'</option>';
        }
        $html .= '</select>';

        return $html;
	}
	/**
	 *
	 * Admin functions
	 *
	 */
	public function process_for_list(&$products)
	{
		foreach($products as $product)
		{
			$this->process_coverimage($product);
			$this->process_category($product);
			$this->process_price($product);
			$this->process_searchable_list($product);
			$this->process_public_list($product);
			$this->process_featured_list($product);
			$this->process_deleted_list($product);
			$this->process_title($product);
		}
	}

	/*we need a way to cache these images t the products.
	otherwise there will always be a lot of unnesseary transc*/
	private function process_coverimage(&$product)
	{
		//image class
		$_hclass = ($product->public) ? "" : "_hidden" ;

		//default
		$product->cover_image = "<div class='img_48 img_noimg'></div>";

		if($this->db->table_exists('nct_product_gallery'))
		{
			$this->load->model('nitrocart_gallery/images_m');

			$r = $this->images_m->get_cover($product->id);

			if($r)
			{
				$product->cover_image = "<img src='files/thumb/".$r->file_id."/50' alt='' id='sf_img_".$product->id."' class='$_hclass' />";
			}

		}
	}

	/**
	 * Here we want to set category_data to be the category name assigned to the product.
	 * The problem is that categories is a seperate module so this function needs to be re-written or r-though.
	 */
	private function process_category(&$product)
	{
		$product->_category_data = 'Unable to process';
	}

	/*same as category_data but show the default (or lowest) price*/
	private function process_price(&$product)
	{
		$product->_price_data = "<span class='s_status s_complete'>$</span>";
	}

	private function process_title(&$product)
	{
		if ($product->deleted == NULL)
			$product->_title_data = anchor(NC_ROUTE.'/products/product/'.$product->slug,$product->name, 'target="_blank" class="" ');
		else  
			$product->_title_data = anchor(NC_ROUTE.'/products/product/'.$product->slug,$product->name, 'target="_blank" class=""style="color:#777" ');

	}
	private function process_searchable_list(&$product)
	{
		$call = ($product->deleted == NULL)?"href='javascript:setfield({$product->id},1)'":'';
		$title = ($product->deleted == NULL)?"title='".lang('nitrocart:products:click_to_change')."'" : '';
		$id = ($product->deleted == NULL)? "id='nitrocart_col_1_{$product->id}'" : '';
		$class = ($product->searchable)?"icon-ok":"icon-minus";
		$tooltip = ($product->deleted == NULL)?"tooltip-s":"";	
		$tag = ($product->deleted == NULL)?"a":"span";	
		$product->_searchable_data = "<{$tag} {$call} {$title} class='{$tooltip} {$class}' status='{$product->searchable}' {$id}></{$tag}>";
	}

	private function process_public_list(&$product)
	{
		$call = ($product->deleted == NULL)?"href='javascript:setfield({$product->id},2)'":'';
		$title = ($product->deleted == NULL)?"title='".lang('nitrocart:products:click_to_change')."'" : '';
		$id = ($product->deleted == NULL)? "id='nitrocart_col_2_{$product->id}'" : '';
		$class = ($product->public)?"icon-ok":"icon-minus";
		$tooltip = ($product->deleted == NULL)?"tooltip-s":"";	
		$tag = ($product->deleted == NULL)?"a":"span";	
		
		$product->_public_data = "<{$tag} {$call} {$title} class='{$tooltip} {$class}' status='{$product->public}' {$id}></{$tag}>";
	}
	private function process_featured_list(&$product)
	{
		$call = ($product->deleted == NULL)?"href='javascript:setfield({$product->id},3)'":'';
		$title = ($product->deleted == NULL)?"title='".lang('nitrocart:products:click_to_change')."'" : '';
		$id = ($product->deleted == NULL)? "id='nitrocart_col_3_{$product->id}'" : '';
		$class = ($product->featured)?"icon-ok":"icon-minus";
		$tooltip = ($product->deleted == NULL)?"tooltip-s":"";	
		$tag = ($product->deleted == NULL)?"a":"span";	
		$product->_featured_data = "<{$tag} {$call} {$title} class='{$tooltip} {$class}' status='{$product->featured}' {$id}></{$tag}>";
	}

	private function process_deleted_list(&$product)
	{
		$product->_deleted_data =  ($product->deleted == NULL)?'':"<span class='stags red'>Deleted</span>";	
	
	}


	/**
	 * Front end functions
	 *
	 */
	public function processSingle($product, $admin_as_customer=false)
	{
		if($this->checkForPermission($product,$admin_as_customer))
		{
			return (array) $product;
		}
		return [];
	}


	private function checkForPermission($product,$admin_as_customer)
	{
		if( (!$product) OR ($admin_as_customer == 'customer' && group_has_role('nitrocart', 'admin_r_catalogue_view') ) )
		{
			if( ( $product == false )  OR  
				( $product->deleted != NULL )  OR  
				( $product->public == 0 ) ) 
			{
				return false; 
			}
		}
		return true; 
	}

	private function setupFlashMessages($param, $product, $session, $admin_as_customer = true)
	{
		if(group_has_role('nitrocart', 'admin_r_catalogue_edit') OR group_has_role('nitrocart', 'admin_r_catalogue_view') && $admin_as_customer == false)
		{

			$notmsg = 'You are viewing this product as an Administrator &bull; <a target="_new" href="'.base_url().NC_ADMIN_ROUTE.'/product/edit/'.$param.'">click here to edit this item</a>';
			$session->set_flashdata(JSONStatus::Error, $notmsg );


			if($product->public == 0)
			{
				$session->set_flashdata(JSONStatus::Error, lang('nitrocart:messages:product_is_hidden') );
			}

			if($product->deleted != NULL)
			{
				$session->set_flashdata(JSONStatus::Error, lang('nitrocart:messages:product_is_deleted') );
			}
		}
	}

	/**
	 * Processes the variances for Display
	 */
	public function getProductVariances( $product_id , $param_x = "")
	{
		// 1. Prepare the X element(param)
		$x_array = explode ( ',' , $param_x);

		// 2. Initi fields/variables for use
		$in_object = new ViewObject();
		$in_object->returnarray = [];
		$in_object->product_id = $product_id;
		$in_object->applyDisc  = (in_array( "NODISCOUNT" , $x_array )) ? false: true;


		// 3. Load Models and Libs
		$this->load->model('nitrocart/products_variances_m');


		// 4. Get the prices, and modules - only get the available variances
		$variances 		=  $this->products_variances_m->get_by_product_front($in_object->product_id) ;


		// 5. Count total variances for output field in plugin
		$in_object->total = count($variances);


		// 6. Build the array for use in plugin
		foreach ($variances as $key => $value)
		{
			$in_object->returnarray[$key] =[];
			$in_object->returnarray[$key]['variant_id'] = $value->id;
			$in_object->returnarray[$key]['variant_text'] = $value->name; //we now store the name on the table so no further lookups of variant name
			$in_object->returnarray[$key]['variant_rrp'] = number_format($value->rrp,2);
			$in_object->returnarray[$key]['variant_base'] = number_format($value->base,2);
			$in_object->returnarray[$key]['variant_price'] = number_format($value->price,2); //this can get changed based on disc modules
			$in_object->returnarray[$key]['variant_discountable'] = $value->discountable;
			$in_object->returnarray[$key]['variant_original_price'] = number_format($value->price,2); //does not get changed by other modules
		}


		// 7. Need the variances to be updated by discounts module
		Events::trigger('SHOPEVT_GetVariances', $in_object );


		$ret_array = [];
		$ret_array[] = ['total' => $in_object->total  , 'variances'=> $in_object->returnarray];

		// 8. Return the array/results
		return $ret_array;
	}

}
// END Cart Class