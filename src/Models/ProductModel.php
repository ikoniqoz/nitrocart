<?php namespace Nitrocart\Models;

use Nitro\Models\Model;
use Nitrocart\Exceptions;
/**
 * @author Sal Bordonaro
 */
class ProductModel extends \Nitro\Models\Model {

	protected $ci;

	/**
	 * @constructor
	 */
	public function __construct() {
		// Get the ci instance
		parent::__construct();
	}

	/**
	 * Load the product integrating with CI + Streams
	 */
	public static function GetProduct($id) {
		
		$ci = get_instance();		
		$ci->load->driver('Streams');

		//$ci->db->set_dbprefix(SITE_REF.'_');

		//load the prod, even for a postback, we need some info about what we are a changn'
		$product = $ci->streams->entries->get_entry($id, 'products', 'nc_products',  false);



		if(! $product)
		{
			throw new \Nitrocart\Exceptions\ProductNotFoundException;
		}	

		//add the product as a payload object if it is deleted then throw the exception
		if($product->deleted != NULL)
		{
			throw new \Nitrocart\Exceptions\ProductDeletedException(null,null,$product);			
		}

		self::GetProductPriceRecords( $product );
		
		//return the object
		return $product;	
	}

	public static function StreamsEntryForm( & $product ) {
		$ci = get_instance();		
		$ci->load->driver('Streams');

		$options = self::_AdminStreamProductViewOptions();

		$product->product_form = $ci->streams->cp->entry_form('products', 'nc_products', 'edit', $product->id, $options->view_override, $options->extra, $options->skips,$options->tabs, $options->hidden,$options->defaults);
	}


	public static function AssignStreamFields(&$product) {

		foreach($product->stream_fields as $key => $field) {
			$field->value = $product->{$field->field_slug}; //set the value
			$product->stream_fields[$key] = $field;
		}
	}


	public static function _AdminStreamProductViewOptions() {

		$ci = get_instance();		

		$options = (object) [];	
		$options->view_override = false;
		$options->extra =  ['cancel_url'=>NC_ADMIN_ROUTE . '/products/', 'return'  => NC_ADMIN_ROUTE . '/product/edit/-id-','allow_add_another'=>true,'insert_url'=>NC_ADMIN_ROUTE . '/product/create' ];
		$options->skips = ['deleted'];
		$options->tabs = false;
		$options->hidden = ['type_slug','type_id'];
		$options->defaults = [];

		if( ! $ci->config->item('admin/show_product_featured_field'))
		{
			$options->hidden[] = 'featured';
		}
		if( ! $ci->config->item('admin/show_product_views_field'))
		{
			$options->hidden[] = 'views';
		}
		if( ! $ci->config->item('admin/show_product_points_field'))
		{
			$options->hidden[] = 'points';
		}
		if( ! $ci->config->item('admin/show_product_slug_field'))
		{
			$options->hidden[] = 'slug';
		}		

		return $options;
	}


	public static function GetProductPriceRecords( & $product )
	{
		$ci = get_instance();		
		$ci->load->model('nitrocart/admin/products_variances_admin_m');
	
		$product->prices = $ci->products_variances_admin_m->get_by_product($product->id);
	}	


}