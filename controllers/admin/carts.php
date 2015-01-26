<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Carts extends Admin_Controller
{
	// Set the section in the UI - Selected Menu
	protected $section = 'carts';
	protected $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');		
		$this->data = new ViewObject();
		$this->load->model('nitrocart/admin/carts_admin_m');
        $this->template
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}


	/**
	 * List all active carts
	 * @return [type] [description]
	 */
	public function index()
	{
		$limit = 20;
		$total_rows = $this->carts_admin_m->count_carts();
		$this->data->pagination = create_pagination( NC_ADMIN_ROUTE . '/carts/', $total_rows, $limit, 4);		
		$this->data->carts =  $this->carts_admin_m->get_carts(); 
		$this->template
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/carts/list', $this->data);		
	}

	/**
	 * View contents of the carts
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function view($id)
	{
		$this->data->items = $this->carts_admin_m->get_cart($id);
		$this->data->username = user_displayname($id,false); 
		$this->template
				->set_layout(false)
				->title($this->module_details['name'])
				->enable_parser(true)		
				->build('admin/carts/view', $this->data );	
	}

	/**
	 * Delete a cart from db but not the session
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function delete($id )
	{
		$this->carts_admin_m->delete($id);
		redirect(NC_ADMIN_ROUTE.'/carts' );
	}

}