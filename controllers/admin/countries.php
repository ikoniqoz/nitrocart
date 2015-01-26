<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Countries extends Admin_Controller
{

	protected $section = 'countries';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');
        $this->load->driver('Streams');
		$this->lang->load('nitrocart/nitrocart_admin_shipping');       
		$this->template->append_css('nitrocart::admin/buttons/font-awesome.min.css') ;
	}

	/**
	 * Fix for url offset
	 * @param  [type] $offset
	 * @return [type]
	 */
	public function index($offset=NULL)
	{

		$extra = [];
		// The title can be a string, or a language
		// string, prefixed by lang:
		$extra['title'] = 'Countries';
		$extra['columns'] = [
		    'id',			
		    'name',
		    'code2',
		    'enabled',
		    'region',
		];

		// We can customize the buttons that appear
		// for each row. They point to our own functions
		// elsewhere in this controller. -entry_id- will
		// be replaced by the entry id of the row.
		$extra['buttons'] = 
		[
			[
				'label' => 'Add State',
				'url' => NC_ADMIN_ROUTE . '/states/create_bycountry/-entry_id-'
			],	
			[
				'label' => 'Edit States',
				'url' => NC_ADMIN_ROUTE . '/states/bycountry/-entry_id-/?filter-states=1&f-country_id=-entry_id-'
			],			
			[
				'label' => lang('global:edit'),
				'url' => NC_ADMIN_ROUTE . '/countries/edit/-entry_id-'
			],						
			[
				'label' => lang('global:delete'),
				'url' => NC_ADMIN_ROUTE . '/countries/delete/-entry_id-',
				'confirm' => true
			]
		];

		// In this example, we are setting the 5th parameter to true. This
		// signals the function to use the template library to build the page
		// so we don't have to. If we had that set to false, the function
		// would return a string with just the form.
		$this->streams->cp->entries_table('countries', 'nc_zones', 10, NC_ADMIN_ROUTE . '/countries/index', true, $extra);		
	}
	/**
	 * List all states
	 */
	public function manage($offset=NULL)
	{

		//get an array of regions

		$data = new ViewObject();

		$params = 
		[
		    'stream'    => 'countries',
		    'namespace' => 'nc_zones',
		    'limit'		=> 10,
		    'sort'		=> 'asc',
		    'order_by'	=> 'name',
		    'paginate'	=> 'yes',
		    'pag_segment'=> 5
		];

		$entries = $this->streams->entries->get_entries($params);



		$data->pagination = $entries['pagination'];
		$data->countries = $entries['entries'];
		$data->total = $entries['total'];


		$data->regions = $this->db->select('region')->distinct('region')->get('nct_countries')->result();
		$oarray = [];
		foreach ($data->regions as $o) 
		{
			$oarray[$o->region]  = $o->region;
		}

		$data->regions = form_dropdown('zone', $oarray);

		$this->template
				->enable_parser(true)
				->title($this->module_details['name'])
				->build('admin/zones_countries/mcl', $data);

	}

	public function create()
	{
		$extra = [
			'return' => NC_ADMIN_ROUTE . '/countries',
			'success_message' => 'Success',
			'failure_message' => 'Error',
			'title' => 'New',
		];
		$skips = [];
		$tabs = false;
		$hidden = ['enabled'];
		$default = ['enabled'=>1];

		//entry_form($stream_slug, $namespace_slug, $mode = 'new', $entry = null, $view_override = false, $extra = array(), $skips = array())
		$this->streams->cp->entry_form('countries', 'nc_zones', 'new', NULL, true, $extra,$skips,$tabs,$hidden,$default);
	}	


	public function edit($id = 0)
	{
		$extra = 
		[
			'return' => NC_ADMIN_ROUTE . '/countries',
			'success_message' => 'Success',
			'failure_message' => 'Error',
			'title' => 'Edit',
		];

		$this->streams->cp->entry_form('countries', 'nc_zones', 'edit', $id, true, $extra);
	}

   /**
	* Delete a FAQ entry
	*
	* @param int [$id] The id of FAQ to be deleted
	* @return void
	*/
	public function delete($id = 0)
	{
		$this->streams->entries->delete_entry($id, 'countries', 'nc_zones');
		$this->session->set_flashdata(JSONStatus::Error, 'Deleted');
		redirect(NC_ADMIN_ROUTE.'/countries/');
	}	
}