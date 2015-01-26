<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Tax extends Admin_Controller
{

	protected $section = 'tax';

	// Common
	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');		

		//check if has access
		role_or_die('nitrocart', 'admin_tax');


		$this->load->model('nitrocart/admin/tax_admin_m');
		$this->load->helper('nitrocart/menu');
        $this->lang->load('nitrocart/nitrocart_admin_tax');


		$this->load->library('form_validation');

		// Set the validation rules
		$this->validation_rules = [
			[
				'field' => 'name',
				'label' => 'lang:label_name',
				'rules' => 'trim|max_length[100]|required'
			],
			[
				'field' => 'rate',
				'label' => 'Rate',
				'rules' => 'trim|numeric|required'
			],
		];

        $this->template
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');		
	}

	// Default page
	public function index()
	{
		$all = $this->tax_admin_m->where('deleted',NULL)->get_all();

		$this->template
				->title($this->module_details['name'])
				->set('taxes',$all)
				->build('admin/tax/tax');
	}



	public function create()
	{
		$data = new ViewObject();

		//postback
		$this->form_validation->set_rules($this->validation_rules);

		if( $this->form_validation->run() )
		{
			$input = $this->input->post();
			$this->tax_admin_m->create($input);
			redirect(NC_ADMIN_ROUTE.'/tax'); //list all
		}

		//also get all the tax records, this is a temp measure untilwe ajaxify the first index page
		$all_tax = $this->tax_admin_m->where('deleted',NULL)->get_all();
		
		
		$data->name = '';
		$data->rate = '';
		$data->id = -1;
		$data->default = 0;

		$this->template
				->set('all_taxes',$all_tax)
				->title('Creating a tax')
				->build('admin/tax/form',$data);

	}

	public function edit($tax_record)
	{

		//postback
		$this->form_validation->set_rules($this->validation_rules);

		//save
		if ($this->form_validation->run())
		{
			$input = $this->input->post();
			$this->tax_admin_m->edit($input['id'], $input);
			redirect(NC_ADMIN_ROUTE.'/tax'); //list all
		}


		//also get all the tax records, this is a temp measure untilwe ajaxify the first index page
		$all_tax = $this->tax_admin_m->where('deleted',NULL)->get_all();
		

		$data = $this->tax_admin_m->get($tax_record);
		$this->template
				->set('all_taxes',$all_tax)
				->title('Edit a tax rate')
				->build('admin/tax/form',$data);

	}


	/**
	 * if products exist that are assigned to the tax we can not delete the tax record!
	 * We should also warn users about such products
	 *
	 */
	public function delete($id = 0)
	{

		//count by the tax id where product is NOT delete
		$this->load->model('nitrocart/products_front_m');
		$v = $this->products_front_m->first_by_tax_id($id);
		if($v)
		{
			$this->session->set_flashdata(JSONStatus::Error,'Can not remove a tax class if it is being used by a product.');
			redirect(NC_ADMIN_ROUTE.'/tax');
		}

		if(is_numeric($id))
		{
			$results = NULL;  //if products exist that have this tax record
			if ($results)
			{
				//show message, can not delete for this reason
			}
			else
			{
				$this->tax_admin_m->delete($id);
			}

		}
		else
		{
			//warn that it was an invalid request
		}

		// Redirect to list all
		redirect(NC_ADMIN_ROUTE.'/tax');
	}

}