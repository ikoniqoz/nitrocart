<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Manage extends Admin_Controller
{
	// Set the section in the UI - Selected Menu
	protected $section = 'manage';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();
		role_or_die('nitrocart', 'admin_manage');
		$this->lang->load('nitrocart/nitrocart_admin_manage');
        $this->template->append_css('nitrocart::admin/deprecated.css');	
	}

	public function index()
	{

		if($input = $this->input->post())
		{
			if($this->input->post('btnAction'))
			{
				//update the settings
				$this->save($input, $this->input->post('btnAction'));
				redirect(NC_ADMIN_ROUTE.'/manage');
			}
		}

		$this->load->model('settings/settings_m');
		$arr = $this->settings_m->where('module','nitrocart')->where('is_gui',false)->order_by('order','asc')->get_all();

		$this->template->title($this->module_details['name'])
			->set('thesettings',$arr)
			->build('admin/manage/main');
	}



	private function save($input, $_action ='save')
	{
		$input = $this->input->post();
		unset($input['btnAction']);

		foreach($input as $key => $value)
		{
			Settings::set($key,$value);
		}

		$this->session->set_flashdata(JSONStatus::Success,'Settings saved');

		if($_action =='save_exit')
		{
			redirect(NC_ADMIN_ROUTE.'/dashboard');
		}
	}


}