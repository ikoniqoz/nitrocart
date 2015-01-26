<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

//use Nitrocart\Ui\Presenters\AdminProductListPresenter as AdminProductListPresenter;


class Products extends Admin_Controller
{

	protected $section = 'products';

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

        // Check permission
		role_or_die('nitrocart', 'admin_r_catalogue_view')  OR role_or_die('nitrocart', 'admin_r_catalogue_edit');

		$this->load->library('nitrocart/products_library');
		$this->load->model('nitrocart/tax_m');
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->lang->load('nitrocart/nitrocart_admin_products');

		$this->mod_path = base_url() . $this->module_details['path'];
		$this->config->load('nitrocart/admin/'.NC_CONFIG);


		$this->presenter = new \Nitrocart\Ui\Presenters\AdminProductListPresenter($this);
		
	}

	public function index($offset = 0)
	{

		//$this->template->active_section = 'products';

		$data = new ViewObject();
		$filter = [];
		$data->filters = new ViewObject();
		$data->filters->modules = [];
		
		$filter['search'] = $data->f_keyword_search = trim($this->_get_filter_setting( 'f_keyword_search', 'display_search_filter' , '') ); //blank for default
		$filter['visibility'] = $data->f_visibility = $this->_get_filter_setting( 'f_visibility', 'display_visibility_filter', 1); //0 is ALL
		$filter['order_by'] = $data->f_order_by = $this->_get_filter_setting( 'f_order_by', 'display_order_filter' , 'id');
		$filter['order_by_order'] = $data->f_order_by_dir = $this->_get_filter_setting( 'f_order_by_dir', 'display_order_by_order_filter' , 'desc');		
		$data->limit = $data->f_items_per_page = $this->_get_filter_setting( 'f_items_per_page', 'display_qty_filter' , 5);
		$filter['status'] = $data->f_status = $this->_get_filter_setting( 'f_status', 'display_status_filter' , 'active');
		$filter['f_featured'] = $data->f_featured = $this->_get_filter_setting( 'f_featured', 'display_f_featured_filter' , 'all');

		$filter['f_filter'] = strtolower($this->_get_filter_setting( 'f_filter', 'display_f_filter_filter' , 'nitrocart,all'));


		$key = $filter['f_filter'];
		//get results/filter
		$filter_values = explode(',' , $filter['f_filter']);
		$data->namespace = trim($filter_values[0]);
		$filter['f_filter'] = trim($filter_values[1]);
		$lib = $data->namespace . '/admin/products_admin_filter_m'; 
		$this->load->model( $lib );
		$total_items = $this->products_admin_filter_m->filter_count($filter);
		$data->pagination = create_pagination( NC_ADMIN_ROUTE . '/products/callback', $total_items, $data->limit, 5);						
		$data->products =  $this->products_admin_filter_m->filter($filter , $data->pagination['limit'] , $data->pagination['offset']);	

		//Get the list of filters to display 
		Events::trigger('SHOPEVT_AdminProductListGetFilters', $data->filters);
		$dropdown_filter = form_dropdown('f_filter',$data->filters->modules,$key);


		// Now process the products for categor, brand images etc..
		$this->products_library->process_for_list($data->products);


		//load all the js required for callbacks later
		$this->presenter->initCall($data,$dropdown_filter);

	}

                  
	public function callback($offset = 0)
	{
		$data = new ViewObject();
		$filter = [];		
		$filters = new ViewObject();
		$filter['search'] =  $data->f_keyword_search = trim($this->_get_filter_setting( 'f_keyword_search', 'display_search_filter' , '',true) ); //blank for default
		$filter['visibility'] = $data->f_visibility   =$this->_get_filter_setting( 'f_visibility', 'display_visibility_filter' , 1,true); //0 is ALL
		$filter['order_by'] = $data->f_order_by  =$this->_get_filter_setting( 'f_order_by', 'display_order_filter' , 'id',true); // 0 is ID
		$filter['order_by_order'] = $data->f_order_by_dir  =$this->_get_filter_setting( 'f_order_by_dir', 'display_order_by_order_filter' , 'desc',true); // 0 is ID
		$data->limit = $data->f_items_per_page =$this->_get_filter_setting( 'f_items_per_page', 'display_qty_filter' , 5,true);
		$filter['status']  = $data->f_status = $this->_get_filter_setting( 'f_status', 'display_status_filter' , 'active',true);
		$filter['f_featured'] = $data->f_featured = $this->_get_filter_setting( 'f_featured', 'display_f_featured_filter' , 'all',true);		
		$filter['f_filter'] = strtolower($this->_get_filter_setting( 'f_filter', 'display_f_filter_filter' , 'nitrocart,all',true));

		//get results/filter
		$filter_values = explode(',' , $filter['f_filter']);
		$data->namespace = trim($filter_values[0]);		
		$filter['f_filter'] = trim($filter_values[1]);		
		$lib = $data->namespace . '/admin/products_admin_filter_m'; 
		$this->load->model( $lib ,'', true );
		$total_items = $this->products_admin_filter_m->filter_count($filter);
		$data->pagination = create_pagination( NC_ADMIN_ROUTE . '/products/callback', $total_items, $data->limit, 5);
		$data->products =  $this->products_admin_filter_m->filter($filter , $data->pagination['limit'] , $data->pagination['offset']);	


		$this->products_library->process_for_list($data->products);


		$this->presenter->callback_build($data);

		/*
		// set the layout to false and load the view
		$this->template
				->set_layout(false)
				->set('pagination', $data->pagination)
				->build('admin/products/line_item',$data);*/
	}

	/**
	 * Gets or sets the filter value for products searching/filtering.
	 * http://labs.builtbyprime.com/tinyphp/
	 * $this->_get_filter_setting( 'f_items_per_page', 'display_qty_filter' , 5)
	 *
	 * @param  string  $f_filter_name       [description]
	 * @param  [type]  $filter_session_name [description]
	 * @param  integer $def_val             [description]
	 * @return [type]                       [description]
	 */
 	//private function _get_filter_setting($a='',$b,$c=0,$d=false){if($d){$e=$this->input->post($a);$this->session->set_userdata($b,$e);return $e;}if($this->input->post($a)){$c=$this->input->post($a);if($this->session->userdata($b)!=$this->input->post($a)){$this->session->set_userdata($b,$c);}}else{$c=($this->session->userdata($b))?$this->session->userdata($b):$c;}return $c;}
	
	private function _get_filter_setting( $f_filter_name = '', $filter_session_name, $def_val = 0 , $pre_save=false)
	{
		if($pre_save)
		{
			$filter_value = $this->input->post($f_filter_name);
			$this->session->set_userdata($filter_session_name, $filter_value );
			return $filter_value;
		}

		if( $this->input->post($f_filter_name) )
		{
			$def_val = $this->input->post($f_filter_name);

			if($this->session->userdata($filter_session_name) != $this->input->post($f_filter_name))
			{
				//save for use later
				$this->session->set_userdata($filter_session_name, $def_val );
			}
		}
		else
		{
			$def_val = ($this->session->userdata($filter_session_name))? $this->session->userdata($filter_session_name) : $def_val ;
		}

		return $def_val;
	}
	


	/**
	 * Action multi-delete
	 */
	public function action()
	{
		// Check for multi delete
		if(  $this->input->post('btnAction') && $this->input->post('action_to') )
		{
			$products = $this->input->post('action_to');
			$this->delete($products);
		}
		redirect(NC_ADMIN_ROUTE.'/products');
	}


	/**
	 * Action delete
	 */
	private function delete( $products = [] )
	{
		// Load all the required classes
		$this->load->model('nitrocart/admin/products_admin_m');

		foreach($products as $key =>$id)
		{
			if( $this->products_admin_m->delete($id) )
			{
				Events::trigger('SHOPEVT_AdminProductDelete', $id);
			}
		}
	}

	/**
	 * Set_field can only process 3 fields /visible/search/featured
	 */
	public function setfield($id, $field='public', $changeto=0)
	{
		// ajax tools and status class
		$this->load->library('nitrocart/Toolbox/Nc_status');
        $status = new NCMessageObject();

		switch($field)
		{
			case 'public':
				$func = 'set_visibility';
				break;
			case 'searchable':
				$func = 'set_searchable';
				break;
			case 'featured':
				$func = 'set_featured';		
				break;
			default:
				$status->setStatus(false,'Unable to identify the field to alter.');
				$status->sendJson();
		} 

		// Load all the required classes
		$this->load->model('nitrocart/admin/products_admin_m');

		// Get the post from the client
		$input = $this->input->post();

		if( $this->products_admin_m->$func( $id , intval($changeto) ))
		{
			Events::trigger('SHOPEVT_AdminProductChanged', $id);
			$status->set('prop',intval( $changeto ));
		}

		$status->sendJson();
	}	
	
	/**
	 * Reset the filter
	 */
	public function filter($action='')
	{
		$this->session->unset_userdata('display_search_filter');
		$this->session->unset_userdata('display_visibility_filter');
		$this->session->unset_userdata('display_order_filter');
		$this->session->unset_userdata('display_qty_filter');
		redirect(NC_ADMIN_ROUTE.'/products');
	}

}