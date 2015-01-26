<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Subsystems extends Admin_Controller
{

	protected $section = 'subsystems';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');		
        $this->template->append_css('nitrocart::admin/admin.css');
        $this->template->append_css('nitrocart::admin/deprecated.css');	 	        
        $this->lang->load('nitrocart/nitrocart_admin_features');  
		$this->load->model('nitrocart/systems_m');
		$this->load->helper('nitrocart/installer');
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ADMIN_ROUTE;
		$this->data = new ViewObject();

		$shortcuts = 
		[
		    ['name' => 'nitrocart:admin:uninstall_module', 'uri' => 'admin/addons/modules/uninstall/nitrocart','class' => 'confirm uninstall'], 
		    ['name' => 'nitrocart:admin:uninstall_systems', 'uri' => 'admin/nitrocart/subsystems/uninstall_all_subsystems','class' => 'confirm uninstall'], 
		];
		//add a section dynamically
        add_template_section($this,'subsystems','Subsystems','admin/nitrocart/subsystems',$shortcuts);       
        add_template_section($this,'features','Features','admin/nitrocart/features');	
  	    
	}


	/**
	 * List all items
	 */
	public function index()
	{
		$subsystems 	= $this->systems_m->where('system_type','subsystem')->order_by('title','asc')->get_all();

		$this->load->library('nitrocart/install_library');

		foreach ($subsystems as $key => $system) 
		{
			//only scan if installed
			if($system->installed ==1)
			{
				$subsystems[$key]->pass_check =  true; //$this->install_library->health( $system->slug , true , 'subsystems' );
			}
			else
			{
				$subsystems[$key]->pass_check =  false; 
			}

		}

		$this->template
				->enable_parser(true)
				->title($this->module_details['name'])
				->set('subsystems',$subsystems)
				->build('admin/subsystems/subsystems');
	}

	/**
	 * List all items
	 */
	public function add($driver)
	{
		$pass = true;
		
		$system = $this->db->where('driver',$driver)->where('system_type','subsystem')->get('nct_systems')->row();
		if($system)
		{
			if($system->require != '')
			{
				$require_system = $this->systems_m->get($system->require);
				if($require_system->installed==0)
				{
					$this->session->set_flashdata(JSONStatus::Error,'Please install ' . $require_system->title . ' first' );
					$pass=false;
				}
			}

		}
		else
		{
			$this->session->set_flashdata(JSONStatus::Error,'No such subsystem Exist');
			$pass=false;
		}


		if($pass)
		{
			//now install the tables
			$this->load->library('nitrocart/install_library');

			if( $this->install_library->subsystem($driver))		
			{
				// Now update the database
				$this->session->set_flashdata(JSONStatus::Success,'Subsystem added');
				$this->systems_m->set_driver_value($driver, 1);
			}

		}


		redirect(NC_ADMIN_ROUTE.'/subsystems');
	}

	public function remove($driver)
	{
		//do not allow uninstall if other modules installed
		$pass=true;

        if($this->db->table_exists('nct_modules'))
        {
            if($this->db->get('nct_modules')->row())
        	{
            	$pass=false;
            	$this->session->set_flashdata(JSONStatus::Error,'You can not uninstall a subsystem when a module is installed.');
            	redirect(NC_ADMIN_ROUTE.'/subsystems');
        	}
        }


		$this->load->library('nitrocart/install_library');



		$results = $this->systems_m->where('require',$driver)->where('installed',1)->get_all();
		if(count($results)>0)
		{
			$this->session->set_flashdata('error','You must first remove other systems that require this subsystem.');
			$pass=false;
		}

		if($pass)
		{
			if( $this->install_library->subsystem($driver,false))			
			{
				$this->session->set_flashdata( JSONStatus::Success,'Subsystem removed');
				$this->systems_m->set_driver_value($driver, 0);
			}
		}

		redirect( NC_ADMIN_ROUTE.'/subsystems');
	}

	public function uninstall_all_subsystems()
	{
		$this->load->library('nitrocart/install_library');
		$this->install_library->uninstall_all_subsystems(); 
		redirect(NC_ADMIN_ROUTE.'/subsystems');
	}

}