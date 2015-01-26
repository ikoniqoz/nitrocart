<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Product_s3 extends Admin_Controller
{

	protected $section = 'products';


	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

        // Check permissions
		role_or_die('nitrocart', 'admin_r_catalogue_view')  OR role_or_die('nitrocart', 'admin_r_catalogue_edit');


		// Create the data object
		$this->data = new ViewObject();


		// Load all the required classes
		$this->load->model('nitrocart/admin/products_admin_m');
		$this->load->model('nitrocart/tax_m');
		$this->load->model('nitrocart/admin/tax_admin_m');
		$this->load->helper('url');
		$this->load->library('session');

		$this->load->library('form_validation');
		//$this->load->library('keywords/keywords');

		$this->lang->load('nitrocart/nitrocart_admin_products');

		$this->mod_path = base_url() . $this->module_details['path'];


		$this->config->load('nitrocart/admin/'.NC_CONFIG);
		
        $this->show_product_other_tab = $this->config->item('admin/show_product_other_tab');

        $this->template
					->enable_parser(true)           
        			->set('base_amount_pricing', NC_BASEPRICING )
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_js('nitrocart::admin/util.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/stags.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css')
					->append_metadata('<script></script>')
					->append_metadata('<script type="text/javascript">' . "\n  var MOD_PATH = '" . $this->mod_path . "';" . "\n</script>");
	}


	//What do we do/or show here ?
	public function index()
	{
		redirect(NC_ADMIN_ROUTE.'/product_s3/create');
	}

	/**
	 * Create a new Product
	 */
	public function create()
	{
		role_or_die('nitrocart', 'admin_r_catalogue_edit');

		// Save:
		if($input = $this->input->post())
		{

			// Setup extra validation rules not applied to the main set
			$this->form_validation->set_rules($this->products_admin_m->_create_validation_rules);

			// If postback validate the form
			if ($this->form_validation->run())
			{
				$input['price'] = (is_numeric($input['price']))?$input['price']:0;

				if ($product_id = $this->products_admin_m->create($input))
				{
					$this->session->set_flashdata(JSONStatus::Success, lang('success'));
					redirect(NC_ADMIN_ROUTE.'/product/edit/'.$product_id);
				}
			}
		}


		//initialize fields
		foreach ($this->products_admin_m->_create_validation_rules AS $rule)
			$this->data->{$rule['field']} = $this->input->post($rule['field']);


		//we need to get a list of packages
		$this->load->model('nitrocart/admin/packages_admin_m');
		$this->load->model('nitrocart/admin/packages_groups_admin_m');
		$this->load->model('nitrocart/admin/products_types_admin_m');
		$this->load->model('nitrocart/zones_m');		
		$this->data->available_packages = $this->packages_admin_m->get_for_admin();
		$this->data->available_groups = $this->packages_groups_admin_m->get_for_admin();
		$this->data->available_taxes = $this->tax_m->get_admin_select();
		$this->data->available_types = $this->products_types_admin_m->get_for_admin();
		$this->data->available_zones = $this->zones_m->get_for_admin();

		$this->data->default_taxID = $this->tax_admin_m->getDefaultID();
		$this->data->default_typeID = $this->products_types_admin_m->getDefaultID(); 
		$this->data->default_groupID = $this->packages_groups_admin_m->getDefaultID();
		$this->data->default_zone_ID = $this->zones_m->getDefaultID();
		

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

		// Build the Template
		$this->template
				->title($this->module_details['name'], lang('nitrocart:products:create'))
				->append_metadata($this->load->view('fragments/wysiwyg', $this->data, true))
				->append_js('nitrocart::admin/products.js')
				->append_js('nitrocart::admin/common.js')
				->build('admin/features/semantics3/create', $this->data);
	}



}