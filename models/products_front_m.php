<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Products_front_m extends MY_Model
{
	/**
	 * The default table for this model
	 * @var string
	 */
	public $_table = 'nct_products';

	public function __construct()
	{
		parent::__construct();
		$this->load->library('nitrocart/Toolbox/Nc_enums');
		$this->load->model('nitrocart/tax_m');
		$this->load->driver('Streams');
	}


	/**
	 * All public access to view a product must channel via here, unless getting a list
	 */
	public function get_product($id, $incr_view = true, $restrict_for_admin=true )
	{
		$method = (is_numeric($id))?'id':'slug';
		$product = parent::get_by( [$method=>$id] );


		if(!$product) 
			return false;


		if($incr_view)
		{
			$this->viewed($product->id);
		}

		if (($product->deleted != NULL) || ($product->public == ProductVisibility::Invisible ))
		{
			//if the user has access to view a hidden product, lets check how they want to view
			if(group_has_role('nitrocart', 'admin_r_catalogue_view'))
			{
				if(!$restrict_for_admin)
				{
					//now we can get the converted, not the raw
					return $this->streams->entries->get_entry($product->id, 'products', 'nc_products', true);
					//return $product;
				}
			}

			return false;
		}

		return $this->streams->entries->get_entry($product->id, 'products', 'nc_products', true);
		//return $product;
	}

	/*
	public function get_product($id, $incr_view = true, $restrict_for_admin=true )
	{
		$method = (is_numeric($id))?'id':'slug';

		$product = parent::get_by( [$method=>$id] );

		if(!$product) return false;

		if($incr_view)
		{
			$this->viewed($product->id);
		}

		if (($product->deleted != NULL) || ($product->public == ProductVisibility::Invisible ))
		{
			//if the user has access to view a hidden product, lets check how they want to view
			if(group_has_role('nitrocart', 'admin_r_catalogue_view'))
			{
				if(!$restrict_for_admin)
				{
					return $product;
				}
			}

			return false;
		}

		return $product;
	}
	*/

	/**
	 *
	 * we do not want this even in admin, at least not in this current version. Deleted is deleted.
	 * We only keep for referencing
	 *
	 * @param  string $mode [public|admin]
	 * @return [Array]       [of products]
	 */
	public function get_all()
	{
		//$this->db->select('nct_products.*');
		$this->where('nct_products.public',1);
		$this->where('nct_products.deleted',NULL);
		return parent::get_all();

		/*
		$params = array(
		        'stream'        => 'products',
		        'namespace'     => 'nc_products',
		        'where'			=> 'public=1&deleted=NULL',
		);
		//$this->streams->entries->get_entries($params)
		*/
	}


	/**
	 * Add view counter
	 *
	 * @param  [type] $product_id [description]
	 * @return [type]             [description]
	 */
	private function viewed($product_id)
	{
		$this->db->select('nct_products.id, nct_products.views');
		$item = parent::get_by('nct_products.id', $product_id);
		return  $this->update($product_id, ['views'=>intval($item->views + 1)] );
	}


	/**
	 * @description This is used for the front end shop, do not use products_m->filter() at the Public site
	 *
	 * @param unknown_type $data
	 * @param unknown_type $limit
	 * @param unknown_type $offset
	 */
	public function filter( $filter, $limit, $offset = 0 )
	{
		// Start filtering now
		$this->db->reset_query();

		//Control will be done via a plugin
		$order_by = 'id';
		$order_dir = 'asc';

		// Get the filtered Count
		$items = $this->where('public', ProductVisibility::Visible )
					->where('deleted', NULL )
					->where('searchable', 1 )
					->order_by($order_by , $order_dir)
					->limit( $limit , $offset )
					->get_all();


		return $items;
	}



	/*count by that counts al products within subcategories as well*/
	public function filter_count($filter = [] )
	{
		// add to the existing filter the settings for all front end items
		//  - Must be visible
		//  - must be NOT deleted
		//  - must be searchabe
		$filter['public'] = ProductVisibility::Visible;

		//$filter['deleted'] = 1;
		$filter['searchable'] = 1;


		// Initialize fields
		$count = 0;

		// we need to do this as we have now collected the categories
		$this->db->reset_query();

		$this->where('deleted', NULL );

		//count all products by first category and standard fields
		$count = $this->count_by($filter);

		return $count;
	}

	/**
	 * Override MY_Model as we have hidden and deleted
	 *
	 * @return [type] [description]
	 */
	public function count_all()
	{
		$filter = [];
		$filter['public'] = ProductVisibility::Visible;
		$filter['deleted'] = NULL;
		$count = $this->count_by($filter);
		return $count;
	}
}