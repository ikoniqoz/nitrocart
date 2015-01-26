<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Packages_groups extends Admin_Controller
{

	protected $section = 'packages_groups';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		$this->load->library('form_validation');
		$this->load->model('nitrocart/admin/packages_groups_admin_m');

		$this->lang->load('nitrocart/nitrocart_admin_packages');

        $this->template
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_js('nitrocart::admin/packages.js')
                    ->append_js('nitrocart::admin/common.js') 
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');

	}

	/**
	 * List all Installed and Not installed gateways
	 */
	public function index()
	{
		role_or_die('nitrocart', 'admin_packages');
		$this->data->packages_groups = $this->packages_groups_admin_m->get_all_available();
		$this->template
				->title($this->module_details['name'])
				->build('admin/packages/groups/list', $this->data);
	}

	/**
	 * Edit an existing package and validate
	 *
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function edit($id)
	{

		role_or_die('nitrocart', 'admin_packages');

		$this->data->id = $id;

		if($input = $this->input->post())
		{
			$this->form_validation->set_rules($this->packages_groups_admin_m->_create_validation_rules);

			if( $this->form_validation->run() )
			{
				$this->data = $this->packages_groups_admin_m->save($id,$input);
				redirect(NC_ADMIN_ROUTE.'/packages_groups');
			}
			else
			{
				foreach ($this->packages_groups_admin_m->_create_validation_rules AS $rule)
					$this->data->{$rule['field']} = $this->input->post($rule['field']);
			}

		}
		else
		{
			$this->data = $this->packages_groups_admin_m->get($id);
		}

		$this->template
				->title($this->module_details['name'])
				->build('admin/packages/groups/edit',$this->data);
	}

	/**
	 * Create a new package
	 *
	 * On post back check and validate input
	 *
	 * @return [type] [description]
	 */
	public function create()
	{
		role_or_die('nitrocart', 'admin_packages');

		$this->data = new ViewObject();

		if($input = $this->input->post())
		{
			$this->form_validation->set_rules($this->packages_groups_admin_m->_create_validation_rules);

			if( $this->form_validation->run() )
			{
				if($id = $this->packages_groups_admin_m->create($input))
				{
					$this->session->set_flashdata(JSONStatus::Success,'Package has been created.');
					redirect(NC_ADMIN_ROUTE.'/packages_groups');
				}
			}
		}

		foreach ($this->packages_groups_admin_m->_create_validation_rules AS $rule)
			$this->data->{$rule['field']} = $this->input->post($rule['field']);

		$this->template
				->title($this->module_details['name'])
				->build('admin/packages/groups/edit',$this->data);
	}


	/**
	 * Duplicate the package group
	 */
	public function duplicate($id)
	{
		role_or_die('nitrocart', 'admin_packages');


		if($new_id = $this->packages_groups_admin_m->duplicate($id))
		{
			$this->session->set_flashdata(JSONStatus::Success,'Package has been copied.');
			redirect( NC_ADMIN_ROUTE. '/packages_groups/edit/'.$new_id);
		}

		$this->session->set_flashdata(JSONStatus::Error,'Failed to duplicate package..');

		redirect(NC_ADMIN_ROUTE.'/packages_groups');
	}

	/**
	 * Delete a package
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function delete($id)
	{
		role_or_die('nitrocart', 'admin_packages');

		if($this->packages_groups_admin_m->delete($id))
			$this->session->set_flashdata(JSONStatus::Success,'Package Group has been removed.');
		else
			$this->session->set_flashdata(JSONStatus::Error,'You cant delete a Package Group if products or packages are assigned to it, or if is the last one.');

		redirect(NC_ADMIN_ROUTE.'/packages_groups');
	}
}