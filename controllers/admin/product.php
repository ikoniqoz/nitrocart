<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Nitrocart\Models\ProductModel as ProductModel;
use Nitrocart\Ui\ProductIntegrator as ProductIntegrator;
use Nitrocart\Ui\Presenters\AdminProductPresenter as AdminProductPresenter;
use Nitrocart\Exceptions\ProductDeletedException as ProductDeletedException;
use Nitrocart\Exceptions\ProductNotFoundException as ProductNotFoundException;



/***{COMMON_CLASS_HEADER}***/
class Product extends Admin_Controller
{

	/**
	 * Let the cms know what section we are active on
	 */
	protected $section = 'products';


	/**
	 * Handle for the presenter
	 */
	protected $presenter;



	/**
	 * main constructor
	 * check for permissions, handle ui setup, lib loading
	 */
	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

        // Check permissions
		role_or_die('nitrocart', 'admin_r_catalogue_view') OR role_or_die('nitrocart', 'admin_r_catalogue_edit');

		// Create the data object
		$this->data = new ViewObject();

		// Load all the required classes
		$this->load->model('nitrocart/admin/products_admin_m');
		$this->load->model('nitrocart/tax_m');
		$this->load->model('nitrocart/admin/tax_admin_m');
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->library('form_validation');

		$this->lang->load('nitrocart/nitrocart_admin_products');

		$this->mod_path = base_url() . $this->module_details['path'];

		$this->config->load('nitrocart/admin/'.NC_CONFIG);
		
		// Create the presenter 
		$this->presenter = new AdminProductPresenter($this);
	}


	//What do we do/or show here ?
	public function index()
	{
		redirect(NC_ADMIN_ROUTE.'/products');
	}

	/**
	 * Create a new Product
	 */
	public function create()
	{


		//
		// Check if the user/group has
		// specific permissions to create
		//
		role_or_die('nitrocart', 'admin_r_catalogue_edit');



		// 
		// Handle the post back
		// code in seperate function for clarity
		//
		if($input = $this->input->post())
		{
			$this->_create($input);
		}


		//
		// initialize fields, either from post back or validation rules
		//
		foreach ($this->products_admin_m->_create_validation_rules AS $rule)
			$this->data->{$rule['field']} = $this->input->post($rule['field']) || '';



		//
		// Load some libs for this task
		/*
		$this->load->model('nitrocart/admin/packages_admin_m');
		$this->load->model('nitrocart/admin/packages_groups_admin_m');
		$this->load->model('nitrocart/admin/products_types_admin_m');
		$this->load->model('nitrocart/zones_m');
		*/



		//
		// Collect some ui objects
		// 
		AdminProductPresenter::GetDropdownOptions( $this->data );
		

		//
		// Determin if we can be here, we need
		// min of 1 of the following
		// 
		Nitrocart\AdminRelocator::ToProductsIf( count($this->data->available_types)	 <= 0 ,  "You must first <a class='create' href='".NC_ADMIN_ROUTE."/products_types/create'>create</a> a product type.");		
		Nitrocart\AdminRelocator::ToProductsIf( count($this->data->available_groups )  <= 0 ,  "You must first <a class='create' href='".NC_ADMIN_ROUTE."/packages_groups/create'>create</a> a package group.");
		Nitrocart\AdminRelocator::ToProductsIf( count($this->data->available_packages) <= 0 ,  "You must first <a class='create' href='".NC_ADMIN_ROUTE."/packages'>create</a> a package.");


		/*
		if(count($this->data->available_groups)<= 0)
		{
			$this->session->set_flashdata(JSONStatus::Error, "You must first <a class='create' href='".NC_ADMIN_ROUTE."/packages_groups/create'>create</a> a package group.");
			redirect(NC_ADMIN_ROUTE.'/products');
		}
		if(count($this->data->available_packages)<= 0)
		{
			$this->session->set_flashdata(JSONStatus::Error, "You must first <a class='create' href='".NC_ADMIN_ROUTE."/packages'>create</a> a package.");
			redirect(NC_ADMIN_ROUTE.'/products');
		}
		if(count($this->data->available_types)<= 0)
		{
			$this->session->set_flashdata(JSONStatus::Error, "You must first <a class='create' href='".NC_ADMIN_ROUTE."/products_types/create'>create</a> a product type.");
			redirect(NC_ADMIN_ROUTE . '/products');
		}
		*/

		//
		// Present the product to the user
		//
		$this->presenter->create($this->data);
	}


	private function _create($input = null)
	{
		if( ($input != null) && (is_array($input)) )
		{
			// Setup extra validation rules not applied to the main set
			$this->form_validation->set_rules($this->products_admin_m->_create_validation_rules);

			// If postback validate the form
			if ($this->form_validation->run())
			{


				$input['price'] = (is_numeric($input['price']))?$input['price']:0;


				/**
				 * Attempt to create the product
				 */
				$product_id = $this->products_admin_m->create($input);



				/**
				 * only redirect if the product id is valid
				 */
				Nitrocart\AdminRelocator::ToProductIf( $product_id , lang('success'), JSONStatus::Success);


				/*
				if ($product_id = $this->products_admin_m->create($input))
				{
					$this->session->set_flashdata(JSONStatus::Success, lang('success'));
					redirect(NC_ADMIN_ROUTE.'/product/edit/'.$product_id);
				}
				*/
			}
		}		
	}



	//What do we do/or show here ?
	public function view($id=NULL)
	{
		//call the event
		if($input = $this->input->post())
		{
			echo "You dont have access here";die;
		}

		role_or_die('nitrocart', 'admin_r_catalogue_edit');		

		$this->load->driver('Streams');
		$this->load->library('Streams/Fields');
		$this->load->model('nitrocart/admin/products_variances_admin_m');		

		$product = null;

		// Get the product if deleted, re-assign the prodct via the exception->payload 
		try
		{
			$product = ProductModel::GetProduct($id);
		}
		catch(ProductNotFoundException $e)
		{
			$this->session->set_flashdata(JSONStatus::Error, lang($e->getMessage()));
			redirect(NC_ADMIN_ROUTE.'/products');		
		}
		catch(ProductDeletedException $e)
		{
			// well I guess its ok to view a delete product!
			// what harm can it really do
			$product = $e->getPayload();
		}


		$product->stream_fields = Nitro\Models\StreamModel::GetStreamFields('products', 'nc_products');
		ProductModel::AssignStreamFields($product);


		// Load data
		$product->prices = $this->products_variances_admin_m->get_by_product($product->id);

		// Setup the required values on the product variable
		Nitrocart\Ui\ProductIntegrator::SetupUI($product);


		Events::trigger('SHOPEVT_AdminProductGet', $product);


		// call for sorting of tabs
		Nitrocart\Ui\ProductIntegrator::RequestProductAdminTabs($product);		
		

		// Present the product to the user
		$this->presenter->single($product,'view');
	}


	/**
	 * @param id $id of the product to edit
	 */
	public function edit($id)
	{
		// Check security
		role_or_die('nitrocart', 'admin_r_catalogue_edit');
				
		// ok we are in, but what about libraries
		$this->load->driver('Streams');
		$this->load->library('Streams/Fields');
		$this->load->model('nitrocart/admin/products_variances_admin_m');		

		// Create the ref to the object here
		$product = null;

		try
		{
			$product = ProductModel::GetProduct($id);
		}
		catch(ProductNotFoundException $e)
		{
			$this->session->set_flashdata(JSONStatus::Error, lang($e->getMessage()));
			redirect(NC_ADMIN_ROUTE.'/products');		
		}
		catch(ProductDeletedException $e)
		{
			//redirct to view
			$this->session->set_flashdata(JSONStatus::Error,$e->getMessage());
			redirect(NC_ADMIN_ROUTE.'/product/view/'.$id);	
		}	
		

		// Call the event, to let other modules know we are saving
		if( $input = $this->input->post()) Events::trigger('SHOPEVT_AdminProductSave', $id );
		


		// Build the HTML input form
		ProductModel::StreamsEntryForm( $product );		


		// Setup the required values on the product variable
		ProductIntegrator::SetupUI($product);


		// We need to call GET even if save was caleld
		Events::trigger('SHOPEVT_AdminProductGet', $product);


		// call for sorting of tabs
		ProductIntegrator::RequestProductAdminTabs($product);


		// Get me a product presenter
		$this->presenter->single($product,'edit');		
	}


	/**
	 * Display a product by a variant
	 */
	public function variant($id)
	{
		//1. Get the variant record

		$variant = $this->db->where('id',$id)->get('nct_products_variances')->row();
		//2. now we have a product get the product

		role_or_die('nitrocart', 'admin_r_catalogue_edit');

		$this->load->model('nitrocart/tax_m');
		$this->load->model('nitrocart/e_attributes_m');

		// Get the product and all its goodness
		$product = $this->products_admin_m->get_product( $variant->product_id );

		$product = null;

		try
		{
			$product = ProductModel::GetProduct($variant->product_id);
		}
		catch(ProductNotFoundException $e)
		{
			$this->session->set_flashdata(JSONStatus::Error, lang($e->getMessage()));
			redirect(NC_ADMIN_ROUTE.'/products');		
		}
		catch(ProductDeletedException $e)
		{
			//well I guess its ok to view a delete product!
			// what harm can it really do
			$product = $e->getProduct();
		}

		$product->variant = $variant;
		$product->attributes = $this->e_attributes_m->get_by_variance_id($id);

		//the variant function will display using ajax.. hopefully
		$this->presenter->variant($product);
	}


	public function autosel($view_mode ='edit' , $dir = 'prev', $curr_id=0)
	{
		$view_mode = (in_array($view_mode, ['edit','view']) )? $view_mode : 'view';
		$method = (in_array($dir, ['prev','next']) )? $dir : 'next';

		//ensure that 
		$method = ($dir=='next') ? 'get_next' : 'get_prev'; 

		$next_id = $this->products_admin_m->$method($curr_id);

		redirect(NC_ADMIN_ROUTE."/product/{$view_mode}/{$next_id}");
	}


	/**
	 *
	 * @param INT $id The ID of the product to duplicate
	 * @access public
	 */
	public function duplicate( $id = 0, $mode='list' )
	{
		role_or_die('nitrocart', 'admin_r_catalogue_edit');

		$ret_object = []; 
		$ret_object['status'] = false;

		// Set the default redirect page
		$redir = NC_ADMIN_ROUTE . '/products';

		if (is_numeric($id))
		{

			$product_id = $this->products_admin_m->duplicate($id);

			if ($product_id)
			{

				$this->session->set_flashdata(JSONStatus::Success, lang('nitrocart:products:duplicate_success'));

				$redir = NC_ADMIN_ROUTE . '/product/edit/'.$product_id;

				if($mode=='list')
					$redir = NC_ADMIN_ROUTE . '/products';

				$ret_object['status'] = true;

			}
			else
			{
				$this->session->set_flashdata(JSONStatus::Error, lang('nitrocart:products:duplicate_failed'));
			}

		}

		if($this->input->is_ajax_request())
		{
			json_encode($ret_object);die;
		}

		redirect($redir);		
	}

	/**
	 * Delete a product
	 */
	public function delete($id = 0)
	{
		role_or_die('nitrocart', 'admin_r_catalogue_edit');

		if (is_numeric($id))
		{
			if($result = $this->products_admin_m->delete($id))
			{
				$this->session->set_flashdata(JSONStatus::Success, lang('nitrocart:products:delete_success'));
				Events::trigger('SHOPEVT_AdminProductDelete', $id);				
			}
			else
			{
				$this->session->set_flashdata(JSONStatus::Error, lang('nitrocart:products:delete_failed'));
			}
		}
		else
		{
			//try to delete by slug!
			if($product = $this->products_admin_m->get_product( $id ))
			{
				//so we dont end up in a slilly endless loop
				$id = (int) $product->id;
				$this->delete($id);
			}

		}

		redirect(NC_ADMIN_ROUTE.'/products');
	}
}