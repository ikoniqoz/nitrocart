<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Zones_m extends MY_Model
{

	public $_table = 'nct_zones';


	public function __construct()
	{
		parent::__construct();
	}


	public function create($input)
	{

		if($input['default'] == 1)
		{
			$this->resetDefault();
		}

		$insert = array(
			'created'=>  date("Y-m-d H:i:s"), 
			'created_by' => $this->current_user->id, 
			'ordering_count'=>0,
			'name'=>$input['name'],
			'description'=>'',
			'default'	=> $input['default'],
		);
		
		return  $this->insert($insert);
	}
	


	public function edit($id,$input)
	{
		if($input['default'] == 1)
		{
			$this->resetDefault();
		}

		$update = array( 
			'name'=>$input['name'], 
			'default'=> $input['default'] 
		);

		return $this->update($id, $update);
	}

	public function resetDefault()
	{
		$this->db->update('nct_zones', ['default'=>0] );
	}



	public function get_countries_by_zone($zone_id,$limit=10,$offset=0)
	{
		return	$this->db->where('zone_id',$zone_id)->limit($limit,$offset)->get('nct_zones_countries')->result();
	}

	public function byzone_count($zone_id)
	{
		return $this->db->where('zone_id',$zone_id)->from('nct_zones_countries')->count_all_results();
	}


	/**
	 * prepare the array so it can be used as a dropdown
	 */
	public function get_for_admin($add_mcl = true)
	{
		$return_array = [];
		$r = $this->get_all();

		if($add_mcl) 
			$return_array[0]='Default [MCL]';
		
		foreach($r as $key=>$value)
		{
			$return_array[$value->id]=$value->name;
		}
		return $return_array;
	}

	public function getDefaultID()
	{
		$row = $this->db->where('default',1)->get('nct_zones')->row();
		if($row)
		{
			return $row->id;
		}
		return 0;
	}


	public function zone_a_country($zone_id,$country_id)
	{
		//only zone if it doesnt exist
		if(!( $this->db->where('zone_id',$zone_id)->where('country_id',$country_id)->get('nct_zones_countries')->row()) )
		{
			if($arow = $this->db->where('id',$country_id)->get('nct_countries')->row())
			{
				//not found
				$status = $this->db->insert('nct_zones_countries', array('country_t'=>$arow->name, 'zone_id'=>$zone_id, 'country_id' =>$country_id, 'created' => date("Y-m-d H:i:s"),'created_by'  => $this->current_user->id ) );
				if($status)
				{
					return true;
				}
			}
		}

		return false;

	}

	public function info()
	{
		$p = [];
		$products = $this->db->where('deleted',NULL)->get('nct_products')->result();

		if(count($products))
		{
			foreach($products as $prod)
			{
				$p[] = $prod->id;
			}

			$result = $this->db->where('zone_id <=',0)->where('deleted',NULL)->where_in('product_id',$p)->get('nct_products_variances')->result();

			return $result;
		}

		return [];
	}

}