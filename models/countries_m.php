<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Countries_m extends MY_Model
{

	public $_table = 'nct_countries';


	public function __construct()
	{
		parent::__construct();
	}

	public function enable($id,$enable=1)
	{
		$edit['enabled'] = $enable;
		return $this->db->where('id',  $id )->update('nct_countries', $edit);
	}
	public function disable($id)
	{
		$edit['enabled'] = 0;
		return $this->db->where('id',  $id )->update('nct_countries', $edit);
	}	

	/*by regions*/
	public function enable_region($region = 'australia_and_oceania',$enable = true)
	{
		$edit['enabled'] = $enable;
		return $this->db->where('region',  $region )->update('nct_countries', $edit);
	}
	public function clearall_regions()
	{
		return $this->db->update('nct_countries', array('enabled'=>0) );
	}	


	public function get_region_dropdown($curr = NULL)
	{
		//get an array of regions
		$regions = $this->select('region')->distinct('region')->get_all();
		$oarray = [];
		foreach ($regions as $o) 
		{
			$oarray[$o->region]  = $o->region;
		}
		return form_dropdown('zone', $oarray);

	}
	public function get_regional_dropdown($curr = NULL)
	{
		$countries = $this->select('id,name,region')->get_all();
		$r=[];
		foreach($countries as $c)
		{
			$r[$c->region][$c->id]  = $c->name;
		}
		
		return form_dropdown('country_id',$r,$curr);
	}


	public function filter($limit=NULL,$offset=0, $only_active=false)
	{
		if($only_active)
		{
			$this->db->where('enabled',1);
		}
		if($limit != NULL)
		{
			$this->db->limit($limit);
		}
		return $this->db->order_by('name')->offset($offset)->get('nct_countries')->result();
	}
	
	public function filter_count($onlyactive=false)
	{
		if($onlyactive)
		{
			$this->db->where('enabled',1);
		}
				
		return $this->db->from('nct_countries')->count_all_results();
	}	


}