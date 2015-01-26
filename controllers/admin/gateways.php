<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Gateways extends Admin_Controller
{

	protected $section = 'gateways';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		//check if has access
		role_or_die('nitrocart', 'admin_checkout');

		$this->lang->load('nitrocart/nitrocart_admin_gateways');

		$this->load->library('nitrocart/gateway_library');
		$this->load->library('form_validation');

        $this->template
        			->enable_parser(true)
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}

	/**
	 * List all Installed and Not installed gateways
	 *
	 *
	 */
	public function index()
	{
		//check available will install any 
		//that requires data being setup in db.
		$this->gateway_library->check_available();

		//retrieve data from db
		$this->data->installed = $this->gateway_library->get_all();

		$this->data->uninstalled = [];
		$this->template
				->title($this->module_details['name'])
				->build('admin/gateways/items', $this->data);
				
	}

	public function edit($id)
	{
		$this->data->gateway = $this->gateway_library->get($id);
		$this->data->options = $this->data->gateway->options;

		//  Load the fields from the Gateway
		$this->form_validation->set_rules($this->data->gateway->fields);

		if ($this->form_validation->run())
		{
			if ($this->gateway_library->edit($this->input->post()))
			{
				$this->session->set_flashdata(JSONStatus::Success, 'Payment gateway has been updated.');
				redirect(NC_ADMIN_ROUTE.'/gateways/');
			}
			else
			{
				// error validating values
				$this->session->set_flashdata(JSONStatus::Error, 'Unable to update Payment Gatway information.');
				redirect(NC_ADMIN_ROUTE.'/gateways/edit/' . $id);
			}
		}

		$this->template
				->title($this->module_details['name'], lang('create'))
				->build('admin/gateways/form', $this->data);
	}

	public function install($slug)
	{
		if ($this->gateway_library->install($slug))
		{
			$this->session->set_flashdata( JSONStatus::Success, lang('success'));
		}
		else
		{
			$this->session->set_flashdata(JSONStatus::Error, lang('error'));
		}

		redirect(NC_ADMIN_ROUTE.'/gateways/');
	}

	public function uninstall($id = 0)
	{
		if (is_numeric($id))
		{
			$result = $this->gateway_library->uninstall($id);

			if (!$result)
			{
				$this->session->set_flashdata(JSONStatus::Error, lang('nitrocart:gateways:delete_error'));
			}
		}

		redirect(NC_ADMIN_ROUTE.'/gateways');
	}

	/**
	 *
	 * @param unknown_type $id
	 * @param Bool $enable true|false if set to false it will disable the gateway
	 * @todo Make this a Ajax call
	 */
	public function enable($id, $enable=1)
	{

		if ($enable)
			$this->gateway_library->enable($id);
		else
			$this->gateway_library->disable($id);

		redirect(NC_ADMIN_ROUTE. '/gateways/');
	}

}