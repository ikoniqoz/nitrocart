<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class States extends Admin_Controller
{

	protected $section = 'states';
	private $data;

	private $reportNames = [];

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');
        $this->load->driver('Streams');
		$this->lang->load('nitrocart/nitrocart_admin_shipping');        
	}


	/**
	 * List all states
	 */
	public function index()
	{	

		// The extra array is where most of our
		// customization options go.
		$extra = [];
		// The title can be a string, or a language
		// string, prefixed by lang:
		$extra['title'] = 'States';
		$extra['columns'] = [
		    'id',			
		    'name',
		    'code',
		    'country_id',
		];		
		// We can customize the buttons that appear
		// for each row. They point to our own functions
		// elsewhere in this controller. -entry_id- will
		// be replaced by the entry id of the row.
		$extra['buttons'] = [
			[
				'label' => lang('global:edit'),
				'url' => NC_ADMIN_ROUTE. '/states/edit/-entry_id-'
			],						
			[
				'label' => lang('global:delete'),
				'url' => NC_ADMIN_ROUTE.'/states/delete/-entry_id-',
				'confirm' => true
			]
		];

		// In this example, we are setting the 5th parameter to true. This
		// signals the function to use the template library to build the page
		// so we don't have to. If we had that set to false, the function
		// would return a string with just the form.
		$this->streams->cp->entries_table('states', 'nc_states', 10, NC_ADMIN_ROUTE.'/states/index', true, $extra);	
	}

	public function bycountry($id)
	{

		$extra = [];
		// The title can be a string, or a language
		// string, prefixed by lang:
		$extra['title'] = 'States';
		$extra['filters'] = [ 'country' => ['country_id'=>$id] ];

		$extra['columns'] = [
		    'id',			
		    'name',
		    'code',
		    'country_id',
		];		

		// We can customize the buttons that appear
		// for each row. They point to our own functions
		// elsewhere in this controller. -entry_id- will
		// be replaced by the entry id of the row.
		$extra['buttons'] = [
			[
				'label' => lang('global:edit'),
				'url' => NC_ADMIN_ROUTE."/states/edit/-entry_id-/{$id}",
			],					
			[
				'label' => lang('global:delete'),
				'url' => NC_ADMIN_ROUTE.'/states/delete/-entry_id-',
				'confirm' => true
			]
		];

		/*$params = array(
		    'stream'    => 'states',
		    'namespace' => 'nc_states'
		);
		$entries = $this->streams->entries->get_entries($params);*/
		//var_dump($entries['entries']);die;
		// In this example, we are setting the 5th parameter to true. This
		// signals the function to use the template library to build the page
		// so we don't have to. If we had that set to false, the function
		// would return a string with just the form.
		$this->streams->cp->entries_table('states', 'nc_states', 10, NC_ADMIN_ROUTE.'/states/bycountry/'.$id, true, $extra);
	}

	public function create($id=NULL)
	{

		$extra = [];
		$extra['return'] = NC_ADMIN_ROUTE.'/states';
		$extra['success_message'] = 'Success';
		$extra['failure_message'] = 'Error';
		$extra['title'] = 'New State';

		$skips = [];
		$tabs = false;
		$hidden = [];
		$default = [];

		if($id!=NULL)
		{
			$skips = [];
			$tabs = false;
			$hidden = [];
			$default = ['country_id'=>$id];
			$country = $this->db->where('id',$id)->get('nct_countries')->row();
			$extra['return'] = NC_ADMIN_ROUTE.'/states/bycountry/'.$id.'?filter-states=1&f-country_id='.$id;
			$extra['title'] = 'New State for ' . (($country->name)?$country->name:'');
		}

		$this->streams->cp->entry_form('states', 'nc_states', 'new', NULL, true, $extra,$skips,$tabs,$hidden,$default);


	}

	public function create_bycountry($country_id)
	{
		$entry_data = [
			'name' => 'New State',
			'code' => 'NS',
		    'country_id' => $country_id,
		];
		$id = $this->streams->entries->insert_entry($entry_data, 'states', 'nc_states', [] );
		redirect(NC_ADMIN_ROUTE.'/states/edit/'.$id);
	}	

	public function edit($id = 0,$country_id=NULL)
	{
		$extra = [
			'return' => NC_ADMIN_ROUTE.'/states',
			'success_message' => 'Success',
			'failure_message' => 'Error',
			'title' => 'Edit',
		];

		if($country_id !== NULL)
		{
			$extra['return'] = NC_ADMIN_ROUTE.'/states/bycountry/'.$country_id."?filter-states=1&f-country_id=$country_id";
		}

		$skips = [];
		$tabs = false;
		//we do not allow country_id to be changed here, so we hide it.
		$hidden = array();// array('country_id');
		$default = array('country_id'=>$country_id);

		$this->streams->cp->entry_form('states', 'nc_states', 'edit', $id, true, $extra,$skips,$tabs,$hidden,$default);

	}

   /**
	* Delete a FAQ entry
	*
	* @param int [$id] The id of FAQ to be deleted
	* @return void
	*/
	public function delete($id = 0)
	{
		$this->streams->entries->delete_entry($id, 'states', 'nc_states');
		$this->session->set_flashdata(JSONStatus::Error, 'Deleted');
		redirect(NC_ADMIN_ROUTE.'/states/');
	}	
}