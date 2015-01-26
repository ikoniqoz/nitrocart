<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Countries extends Public_Controller
{

	protected $section = 'api';

	public function __construct()
	{
		parent::__construct();

        // call event for extention module integration
        Events::trigger('SHOPEVT_ShopPublicController');

	}

	public function index()
	{

	}

	
	/**
	 * [CurrentUserCanExpress description]
	 * @param [type] $country_id 	The ID of the Country
	 * @param string $method The valid method to choose, states,add,remove ect..
	 */
	public function country($country_id,$method='states')
	{
		switch ($method) 
		{
			case 'states':
				$this->states($country_id);
				break;
			
			default:
				# code...
				break;
		}
	}

	private function states($country_id)
	{
		$response = array();
		$response['status'] = JSONStatus::Success;
		$response['action'] = 'api/countries/country/<ID>/states';		
		$response['message'] = '';
		$response['html'] = $this->build_options($country_id);

		echo json_encode($response);die;
	}

	private function build_options($country_id)
	{
		$country = $this->db->where('code2',$country_id)->get('nct_countries')->row();

		$states = $this->db->where('country_id',$country->id)->order_by('name','asc')->select('name,code')->get('nct_states')->result();

		$html = '';
		$html .= "<option value='---'>--Please select a country--</option>";
		foreach ($states as $state)
		{
			$html .= "<option value='{$state->code}'>{$state->name}</option>";
		}
		
		return $html;
	}

}
