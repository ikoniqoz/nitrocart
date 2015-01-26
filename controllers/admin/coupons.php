<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Coupons extends Admin_Controller
{
	// Set the section in the UI - Selected Menu
	protected $section = 'coupons';
	protected $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');		
		$this->load->model('nitrocart/admin/coupons_admin_m');
		$this->data = new ViewObject();
	}

	/**
	 * List all the coupons here with pagination
	 * @return [type] [description]
	 */
	public function index()
	{
		$limit = 20;
		$total_rows = $this->coupons_admin_m->where('deleted',NULL)->count_all();
		$this->data->pagination = create_pagination(NC_ADMIN_ROUTE.'/coupons/', $total_rows, $limit, 4);		
		$this->data->coupons =  $this->coupons_admin_m->where('deleted',NULL)->limit($this->data->pagination['limit'] , $this->data->pagination['offset'])->get_all();	

		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/coupons/list', $this->data);		
	}

	public function create()
	{
		if($input = $this->input->post())
		{
			$this->coupons_admin_m->create($input);
			redirect(NC_ADMIN_ROUTE.'/coupons');	
		}
		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/coupons/create');				
	
	}

	public function edit($id)
	{
		if($input = $this->input->post())
		{
			$this->coupons_admin_m->edit($id,$input);
			redirect(NC_ADMIN_ROUTE.'/coupons');	
		}		
		$data = $this->coupons_admin_m->get($id);

		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/coupons/edit', $data);	
	}


	public function delete($id)
	{
		$this->coupons_admin_m->delete($id);
		redirect(NC_ADMIN_ROUTE.'/coupons');
	}



}