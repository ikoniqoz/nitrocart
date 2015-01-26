<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Search_Library extends ViewObject
{


	protected $file_path = '';


	public function __construct($params = array())
	{
		log_message('debug', "Class Initialized");
	}


	public function re_index_search()
	{

		$this->load->model('search/search_index_m');

		//
		// Delete all from db where == nitrocart
		//
		$this->db->where(array(
				'module'     => 'nitrocart'
			))
			->delete('search_index');

		//now go through all items and index

		$this->load->model('nitrocart/admin/products_admin_m');

		$results = $this->products_admin_m->get_all();

		$count =0;

		foreach($results as $product)
		{
			$this->add_to_search($product->id, $product->name, $product->description) ;
			$count++;
		}


		return $count;
	}


	public function add_to_search($id, $name,$desc)
	{
		// Load the search index model
		$this->load->model('search/search_index_m');


		$this->search_index_m->index(
		    'shop',
		    'nitrocart:product',
		    'nitrocart:products',
		    $id,
		    NC_ROUTE .'/products/product/'.$id,
		    $name,
		    $desc,
		    [
		        'cp_edit_uri'   => NC_ADMIN_ROUTE.'/product/edit/'.$id,
		        'cp_delete_uri' => NC_ADMIN_ROUTE.'/product/delete/'.$id,
		        'keywords'      => NULL,
		    ]
		);

		return true;
	}

	public function remove_from_search($id)
	{
	}


}
// END Cart Class