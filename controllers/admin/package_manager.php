<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Package_manager extends Admin_Controller
{

	protected $section = 'package_manager';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		$this->load->library('form_validation');
		$this->load->model('nitrocart/packages_m');

		$this->lang->load('nitrocart/nitrocart_admin_packages');

        $this->template
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}

	public function index()
	{
		role_or_die('nitrocart', 'admin_packages');
		$this->template
				->title($this->module_details['name'])
				->build('admin/packages/index');
	}

}