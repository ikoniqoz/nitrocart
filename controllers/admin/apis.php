<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Apis extends Admin_Controller
{
	// Set the section in the UI - Selected Menu
	protected $section = 'apis';
	protected $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');		
        $this->load->model('nitrocart/admin/apis_admin_m');
		$this->data = new ViewObject();
	}

	/**
	 * List all active carts
	 * @return [type] [description]
	 */
	public function index()
	{
		$limit = 20;
		$total_rows = $this->apis_admin_m->count_all();
		$this->data->pagination = create_pagination( NC_ADMIN_ROUTE . '/carts/', $total_rows, $limit, 4);	
		$this->data->keys =  $this->apis_admin_m->get_all(); 
		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/apis/list', $this->data);	
	}

	public function create()
	{

		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->apis_admin_m->_validation_fields);

		if ( $input = $this->input->post() AND $this->form_validation->run() )
		{
			$id = $this->apis_admin_m->create($input);
			redirect(NC_ADMIN_ROUTE.'/apis/' );
		}
		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/apis/create');	
	}

	public function edit($id)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->apis_admin_m->_edit_validation_fields);
		if ( $input = $this->input->post() AND $this->form_validation->run() )
		{
			$id = $this->apis_admin_m->edit($id,$input);
			redirect(NC_ADMIN_ROUTE.'/apis' );
		}
		$data = $this->apis_admin_m->get($id);
		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/apis/edit',$data);	
	}	

	/**
	 * View contents of the carts
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function view($id,$offset=0)
	{
		$limit = 10;

		$this->data->keydata = $this->apis_admin_m->get($id);

		$total_items = $this->db->where('key_id',$id)->get('nct_api_requests')->num_rows();

        $this->data->pagination = create_pagination(NC_ADMIN_ROUTE."/apis/view/{$id}/", $total_items, $limit,6);		

		$this->data->items = $this->db->where('key_id',$id)
								->limit($this->data->pagination['limit'], $this->data->pagination['offset'])
								->order_by('id','desc')
								->get('nct_api_requests')->result();

		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/apis/view', $this->data );					
	}


	/**
	 * Delete a cart from db but not the session
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function delete($id )
	{
		$this->apis_admin_m->delete($id);
		redirect(NC_ADMIN_ROUTE.'/apis' );
	}

}