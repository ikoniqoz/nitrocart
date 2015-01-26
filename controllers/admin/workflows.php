<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Workflows extends Admin_Controller
{

	protected $section = 'workflows';

	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		// Load all the required classes
		$this->load->model('nitrocart/workflows_m');
		$this->load->library('form_validation');
        $this->lang->load('nitrocart/nitrocart_admin_workflows');

        $this->template
        			->enable_parser(true)
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}

	/**
	 * List all items
	 */
	public function index()
	{
		//check if has access
		role_or_die('nitrocart', 'admin_workflows');

		$this->data->workflows = $this->workflows_m->order_by('pcent','asc')->get_all();

		$this->template
				->title($this->module_details['name'])
				->build('admin/workflows/list', $this->data);
	}


	/**
	 * Create a new Brand
	 */
	public function create()
	{

		//check if has access
		role_or_die('nitrocart', 'admin_workflows');

		$this->data = new ViewObject();

		// Set validation rules
		$this->form_validation->set_rules($this->workflows_m->_create_validation_rules);

		// if postback-validate
		if ($this->form_validation->run())
		{
			//Get all the POST
			$input = $this->input->post();


			//Create a new collection and retrieve the ID
			$id = $this->workflows_m->create($input);

			//Session message
			$this->session->set_flashdata(JSONStatus::Success, lang('nitrocart:workflow:create_success'));

			if($input['btnAction']=='save_exit')
			{
				redirect(NC_ADMIN_ROUTE.'/workflows/');
			}

			//Redirect
			redirect(NC_ADMIN_ROUTE.'/workflows/edit/'.$id);

		}
		else
		{
			foreach ($this->workflows_m->_create_validation_rules as $key => $value)
			{
				$this->data->{$value['field']} = '';
			}
		}

		// Build page
		$this->template
			->title($this->module_details['name'])
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, true))
			->build('admin/workflows/create', $this->data);
	}


	/**
	 *	We need to alter edit to stop allow changing product.
	 *	Product and collection can not change
	 */
	public function edit( $id = NULL)
	{

		//check if has access
		role_or_die('nitrocart', 'admin_workflows');


		//check if we have an id and if is numeric
		if( ! $id || ! is_numeric($id) )
		{
			$this->session->set_flashdata(JSONStatus::Error, lang('shop_workflows:invalid_id') );
			redirect(NC_ADMIN_ROUTE.'/workflows');
		}

		// Get row
		$row = $this->workflows_m->get($id);


		// Check if exist
		if (!$row)
		{
			$this->session->set_flashdata(JSONStatus::Error, lang('shop_workflows:not_found'));
			redirect(NC_ADMIN_ROUTE.'/workflows');
		}


		$this->data = (object) $row;
		$this->form_validation->set_rules($this->workflows_m->_edit_validation_rules);

		// if postback-validate
		if ($this->form_validation->run())
		{
			$input = $this->input->post();
			$this->workflows_m->edit($id,$input);


			Events::trigger('evt_collection_changed', $id );

			$this->session->set_flashdata(JSONStatus::Success,  lang('shop_collection:update_success'));

			if($input['btnAction']=='save_exit')
			{
				redirect(NC_ADMIN_ROUTE.'/workflows/');
			}

			redirect(NC_ADMIN_ROUTE."/workflows/edit/{$id}");
		}

		// Build page
		$this->template
			->enable_parser(true)
			->title($this->module_details['name'])
			->append_metadata($this->load->view('fragments/wysiwyg', $this->data, true))
			->build('admin/workflows/edit', $this->data);
	}



	/**
	 * Simple delete, will need to work on validation and return messages
	 * @param unknown_type $id
	 */
	public function delete($id = NULL, $ret_cat = 0)
	{
		if($input = $this->input->post())
		{
			if(isset($input['btnAction']))
			{
				$this->_deleteMany();
			}
		}

		//check if has access
		role_or_die('nitrocart', 'admin_workflows');

		//check if we have an id and if is numeric
		if( ! $id || ! is_numeric($id) )
		{
			$this->session->set_flashdata(JSONStatus::Error, lang('shop_workflows:invalid_id') );
			redirect(NC_ADMIN_ROUTE.'/workflows');
		}

		if($this->workflows_m->delete($id))
		{
			Events::trigger('evt_workflows_deleted', $id );

			if($this->input->is_ajax_request())
			{
				echo (json_encode(
						[
						'status'=>JSONStatus::Success,
						] ) );
				exit;
			}

		}

		if($ret_cat>0) redirect(NC_ADMIN_ROUTE.'/workflows/edit/'.$ret_cat);

		redirect(NC_ADMIN_ROUTE.'/workflows');
	}

	private function _deleteMany()
	{

		$input = $this->input->post();


		if(isset($input['action_to']))
		{
			foreach( $input['action_to'] as $key => $value )
			{
				$this->workflows_m->delete( $value );
			}
		}

		redirect(NC_ADMIN_ROUTE.'/workflows');
	}
}