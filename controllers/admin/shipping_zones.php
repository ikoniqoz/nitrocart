<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Shipping_zones extends Admin_Controller
{

	protected $section = 'shipping_zones';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		role_or_die('nitrocart', 'admin_checkout');
		$this->lang->load('nitrocart/nitrocart_admin_shipping');
		$this->load->library('form_validation');
		$this->load->model('nitrocart/countries_m');
		$this->load->model('nitrocart/zones_m');			

		$this->getshowstatus();
	}


	/**
	 * List all items
	 */
	public function index()
	{
		$zones = $this->db->get('nct_zones')->result();
		$this->template
				->set('zones',$zones)
				->enable_parser(true)
				->title($this->module_details['name'])
				->build('admin/shipping/zones/list', $this->data);		
	}

	public function countries($zone_id=0,$offset=0)
	{
	
		$this->data->limit = 10;
		$this->data->zone_id = $zone_id;
		$zone = $this->zones_m->get($zone_id); 

		if(!$zone)
		{
			//oops, cant find that zone
			redirect(NC_ADMIN_ROUTE.'/shipping_zones');
		}

		$total_items = $this->zones_m->byzone_count($zone->id);
		$this->data->pagination = create_pagination(NC_ADMIN_ROUTE.'/shipping_zones/countries/'.$zone->id.'/', $total_items,$this->data->limit, 6);	
		$this->data->zoned_countries = $this->zones_m->get_countries_by_zone($zone->id, $this->data->pagination['limit'],$this->data->pagination['offset']);

		$this->data->regions = $this->countries_m->get_region_dropdown();
		$this->data->countries = $this->countries_m->get_regional_dropdown();

		$this->template
				->set('zone_id',$zone->id)
				->set('zonename',$zone->name)
				->enable_parser(true)
				->title($this->module_details['name'])
				->build('admin/shipping/zones/countries', $this->data);				
	}	


	public function create()
	{	

		if($input = $this->input->post())
		{
			if(isset($input['name']))
			{
				if($this->zones_m->create($input))
				{
					redirect(NC_ADMIN_ROUTE.'/shipping_zones');
				}
			}
		}

		$this->template
				->set('name','')
				->set('default',0)
				->enable_parser(true)
				->title($this->module_details['name'])
				->build('admin/shipping/zones/edit', $this->data);		
	}

	public function edit($zone_id = 0)
	{
		
		$zone = $this->db->where('id',$zone_id)->get('nct_zones')->row();

		if($input = $this->input->post())
		{
			if($this->input->post('name'))
			{
				if($this->zones_m->edit($zone_id,$input))
				{

					redirect(NC_ADMIN_ROUTE.'/shipping_zones');
				}
			}
		}

		$countries = $this->db->select('id,name')->get('nct_countries')->result();
		$r=[];
		foreach($countries as $c)
		{
			$r[$c->id] = $c->name;
		}
		$countries = form_dropdown('country',$r);


		$this->template
				->set('countries', $countries)
				->set('id',$zone_id)
				->set('name',$zone->name)
				->set('default',$zone->default)
				->enable_parser(true)
				->title($this->module_details['name'])
				->build('admin/shipping/zones/edit', $this->data);		
	}


	public function delete($zone_id = 0)
	{

		//remove the group
		$this->db->where('id',$zone_id)->delete('nct_zones');
		
		//remove the assignments
		$this->db->where('zone_id',$zone_id)->delete('nct_zones_countries');


		//now update all variances to have 0 (default) in the zone id
		$this->db->where('zone_id',$zone_id)->update('nct_products_variances', array( 'zone_id' => 0 ) );


		//redirect
		redirect(NC_ADMIN_ROUTE.'/shipping_zones');	
	}

	/**
	 * enaling the country on the default zone list
	 */
	public function country($enable, $id)
	{
	
		$array_data = [];
		$array_data['status'] = JSONStatus::Success;
		$array_data['id'] = $id;

		$status = ($enable) ? true : false ;
		$this->countries_m->enable($id,$status);
		$array_data['is_linked'] = $status;

		//return method
		if($this->input->is_ajax_request())
		{
			echo json_encode($array_data);die;
		}
		else
		{
			redirect(NC_ADMIN_ROUTE.'/shiping_zones/');
		}
	}

	public function country_toggle($id)
	{

		$array_data = [];
		$array_data['status'] = JSONStatus::Error;
		$array_data['id'] = $id;
		$array_data['is_linked'] = 'Unknown';
		$array_data['stage'] = '0';
		$array_data['int_status'] = 0;



		if($country = $this->db->where('id',$id)->get('nct_countries')->row())
		{
			$array_data['stage'] = '1';			
			if($this->countries_m->enable($id,  !($country->enabled)  ))
			{
				$array_data['stage'] = '2';						
				$array_data['status'] = JSONStatus::Success;				
				$country->enabled = !($country->enabled);	
			}

			$array_data['is_linked'] = ($country->enabled)?'Active':'InActive';	
			$array_data['int_status'] = (int) $country->enabled;
		}

		//return method
		if($this->input->is_ajax_request())
		{
			echo json_encode($array_data);die;
		}

		redirect(NC_ADMIN_ROUTE.'/shiping_zones/');
	}



	public function assign_country_to_zone( $zone_id , $country_id )
	{
		$array_data = [];
		$array_data['status'] = JSONStatus::Success;
		$array_data['is_linked'] = false;

		if($row = $this->db->where('zone_id',$zone_id)->where('country_id',$country_id)->get('nct_zones_countries')->row())
		{
			//found
		}
		else
		{
			if($arow = $this->db->where('id',$country_id)->get('nct_countries')->row())
			{
				//not found
				$status = $this->db->insert('nct_zones_countries', ['country'=>$arow->name, 'zone_id'=>$zone_id, 'country_id' =>$country_id, 'created' => date("Y-m-d H:i:s"),'created_by'  => $this->current_user->id ] );
				if($status)
				{
					$array_data['is_linked'] = true;
				}
			}
		}

		//return method
		if($this->input->is_ajax_request())
		{
			echo json_encode($array_data);die;
		}
		else
		{
			redirect(NC_ADMIN_ROUTE.'/shipping_zones/countries/'.$zone_id.'/0');
		}
	}

	public function remove_country_zone_assignment( $zone_id , $country_id )
	{

		$array_data = [];
		$array_data['status'] = JSONStatus::Success;
		$array_data['is_linked'] = false;

		//not found
		$status = $this->db->where('zone_id',$zone_id)->where('country_id',$country_id)->delete('nct_zones_countries');
		if($status)
		{

		}
		
		//return method
		if($this->input->is_ajax_request())
		{
			echo json_encode($array_data);die;
		}
		else
		{
			redirect(NC_ADMIN_ROUTE.'/shipping_zones/countries/'.$zone_id.'/0');
		}
	}	

	/**
	 * from the MCL
	 */
	public function toggle()
	{
		if($input = $this->input->post())
		{
			
			$enable = 0;
			$zone = (isset($input['zone'])) ? $input['zone'] : '' ;
			$mode = (isset($input['mode'])) ? $input['mode'] : 'add' ;

			switch ($mode) 
			{
				case 'Add':
					$enable = 1;
				case 'Remove':
					if($this->countries_m->enable_region($zone,$enable))
					{
						$this->session->set_flashdata('success','Region updated.');
					}
					break;
				default:
					//do nothing
					break;
			}

		}

		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ADMIN_ROUTE.'/shipping_zones';		
		redirect($this->refer);		
	}

	public function remove_all_Regions()
	{
		$this->countries_m->clearall_regions();
		$this->session->set_flashdata('success','All regions cleared.');		
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ADMIN_ROUTE.'/shipping_zones';		
		redirect($this->refer);		
	}

	public function showall()
	{
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ADMIN_ROUTE.'/shipping_zones';
		$this->session->set_userdata('showzones','all');
		redirect($this->refer);		
	}

	public function showactive()
	{
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ADMIN_ROUTE.'/shipping_zones';		
		$this->session->set_userdata('showzones','active');
		redirect($this->refer);			
	}

	public function getshowstatus()
	{
		if(! $this->session->userdata('showzones'))
		{
			$this->session->set_userdata('showzones','all');
		}

		return $this->session->userdata('showzones');
	}	

	public function zoneregion()
	{

		$zone_id = $this->input->post('zone_id');
		$region = $this->input->post('zone'); //region
		$mode = $this->input->post('mode'); //region
		$array_data = [];
		$array_data['status'] = JSONStatus::Success;
		$array_data['is_linked'] = false;

		if($mode=='Remove')
		{
			//get countries by region
			if($results = $this->db->where('region',$region)->get('nct_countries')->result())
			{
				foreach($results as $key=>$country)
				{
					$this->db->where( ['zone_id'=>$zone_id, 'country_id' =>$country->id] )->delete('nct_zones_countries');
				}
			}

		}
		else
		{

			if($results = $this->db->where('region',$region)->get('nct_countries')->result())
			{
				foreach($results as $country)
				{

					if(! $this->db->where('zone_id',$zone_id)->where('country_id',$country->id)->get('nct_zones_countries')->row())
					{
						// row not found
						$status = $this->db->insert('nct_zones_countries', ['country'=>$country->name, 'zone_id'=>$zone_id, 'country_id' =>$country->id, 'created' => date("Y-m-d H:i:s"),'created_by'  => $this->current_user->id ] );
					}

				}
			}

		}
		
		redirect(NC_ADMIN_ROUTE.'/shipping_zones/countries/'.$zone_id.'/0');
	}	

	public function zone()
	{
		if(! $this->input->is_ajax_request())
		{
			if(! $this->input->post())
			{
				redirect(NC_ADMIN_ROUTE.'/shipping_zones/');
			}
		}

		$zone_id = $this->input->post('zone_id');
		$country_id = $this->input->post('country_id');

		$array_data = [];
		$array_data['status'] = JSONStatus::Success;
		$array_data['is_linked'] = true;

		//only zone if it doesnt exist
		$this->zones_m->zone_a_country($zone_id,$country_id);

		//return method
		if($this->input->is_ajax_request())
		{
			echo json_encode($array_data);die;
		}
		else
		{
			redirect(NC_ADMIN_ROUTE.'/shipping_zones/countries/'.$zone_id.'/0');
		}
	}

}