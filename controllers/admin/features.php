<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Features extends Admin_Controller
{

	protected $section = 'features';
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

		$this->config->load('nitrocart/admin/'.NC_CONFIG);

		$this->load->library('nitrocart/features_library');

        $enable_features = $this->config->item('admin/enable_features');
        $enable_features OR redirect($this->refer);

		$this->data = new ViewObject();

		$this->template->enable_parser(true);


		$shortcuts = 
		[
		    ['name' => 'nitrocart:admin:uninstall_features', 'uri' => 'admin/nitrocart/features/uninstall_all_features','class' => 'confirm uninstall'], 
		];  
        add_template_section($this,'subsystems','Subsystems','admin/nitrocart/subsystems');
        add_template_section($this,'features','Features','admin/nitrocart/features',$shortcuts);	        
	}



	/**
	 * List all items
	 */
	public function index()
	{
		$installed = [];
		$subsystems = [];


		$subsystems = $this->features_library->get_drivers();


		$features = $this->systems_m->order_by('title','asc')->where('system_type','feature')->get_all();


		$this->load->library('nitrocart/install_library');


		//make driver the key
		foreach ($features as $key => $system) 
		{	
			$subsystems[$system->driver] = $system;
		}	


		ksort($subsystems);

		$this->template
				->title($this->module_details['name'])
				->set('installed',$installed)
				->set('subsystems',$subsystems)
				->build('admin/subsystems/features');
	}

	private function get_listing()
	{
		$this->load->library('nitrocart/features');
	}

	/**
	 * Add new feature
	 */
	public function add($driver)
	{
		$pass = true;

		$system = $this->db->where('driver',$driver)->where('system_type','feature')->get('nct_systems')->row();
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

			//cant find it in the db, lets see if it exists,...physically
			$system = $this->features_library->get($driver);

			if($system)
			{
				if($system->require != '')
				{
					if($require_system = $this->systems_m->get($system->require))
					{
						if($require_system->installed==0)
						{
							$this->session->set_flashdata(JSONStatus::Error,'Please install ' . $require_system->title . ' first' );
							$pass=false;
						}	
					}
					else
					{
						$this->session->set_flashdata(JSONStatus::Error,'Please install the subsystems first.' );
						$pass=false;
					}
				}
			}
			else
			{
				$this->session->set_flashdata(JSONStatus::Error,'No such App Exist');
				$pass=false;
			}

		}


		if($pass)
		{
			//now install the tables
			$this->load->library('nitrocart/install_library');

			//install the feature
			if( $this->install_library->feature( $driver, true ))
			{
				// Now update the database
				$this->session->set_flashdata(JSONStatus::Success,"Feature {$driver} added");
				$this->systems_m->set_driver_value($driver, 1);
			}
			else
			{
				$this->session->set_flashdata(JSONStatus::Error,"Failed to install Feature {$driver}");
			}

		}

		redirect(NC_ADMIN_ROUTE.'/features');
	}

	/**
	 * Remove feature from system
	 */
	public function remove($driver)
	{

		$pass=true;
		$this->load->library('nitrocart/install_library');
		$results = $this->systems_m->where('require',$driver)->where('installed',1)->get_all();
		if(count($results)>0)
		{
			$this->session->set_flashdata(JSONStatus::Error,'You must first remove other systems that require this feature.');
			$pass=false;
		}

		if($pass)
		{   

			if( $this->install_library->feature( $driver , false ))
			{
				$this->session->set_flashdata(JSONStatus::Success,'Feature removed');
				$this->systems_m->set_driver_value($driver, 0);
			}
		}
		redirect( NC_ADMIN_ROUTE.'/features');
	}

	public function uninstall_all_features()
	{
		$this->load->library('nitrocart/install_library');
		$this->install_library->uninstall_all_features(); 
		redirect(NC_ADMIN_ROUTE.'/features');
	}

}