<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/../packages_groups_m.php');
class Packages_groups_admin_m extends Packages_groups_m
{

	//see parent file
	//public $_table = 'nct_packages_groups';	
	//public $_create_validation_rules = array(

	public function __construct()
	{
		parent::__construct();
	}

	public function create($input)
	{
		if($input['default']==1)
		{
			$this->resetDefaults();
		}

		$to_insert = [
			'name' 		  		=> $input['name'],
			'default' 		  		=> $input['default'],
            'created_by'    	=> $this->current_user->id,
            'created'       	=> date("Y-m-d H:i:s"),
            'updated'       	=> date("Y-m-d H:i:s"),
            'core'       		=> (isset($input['core']))?$input['core']:0,
		];

		$id = $this->insert($to_insert);
		return ($id) ? $id : false;
	}

	public function save($id, $input)
	{
		if($input['default']==1)
		{
			$this->resetDefaults();
		}

		$to_insert = [
			'name' 		  		=> $input['name'],
			'default' 		  	=> $input['default'],
            'updated'       	=> date("Y-m-d H:i:s"),
		];
		return $this->update($id, $to_insert);
	}

	public function duplicate( $id  )
	{
		$row = $this->get($id);
		$to_insert = [
			'name' 		  		=> $row->name.'-copy',
			'default' 		  	=> 0, //cant have 2
            'created_by'    	=> $this->current_user->id,
            'created'       	=> date("Y-m-d H:i:s"),
            'updated'       	=> date("Y-m-d H:i:s"),
		];
		return $this->insert($to_insert);
	}

	/**
	 * prepare the array so it can be used as a dropdown
	 */
	public function get_for_admin()
	{
		$return_array = [];
		$r = $this->where('deleted',NULL)->get_all();
		foreach($r as $key=>$value)
		{
			$return_array[$value->id]=$value->name;
		}
		return $return_array;

	}


	/**
	 * first check to see if we can delete
	 */
	public function delete($id)
	{
		//now check for products
		$count = $this->db->where('pkg_group_id',$id)->where('deleted',NULL)->from('nct_products_variances')->count_all_results();

		if($count > 0)
		{
			return false;
		}

		//do not delete if it is the last one
		$count = $this->db->where('deleted',NULL)->from('nct_packages_groups')->count_all_results();
		if($count === 1)
		{
			return false;
		}


		$count = $this->db->where('pkg_group_id',$id)->where('deleted',NULL)->from('nct_packages')->count_all_results();
		if($count > 0)
		{
			return false;
		}

		// Else ok to delete
		return $this->__delete($id);
	}

	private function __delete($id)
	{
		$package_group = $this->get($id);
		//do not delete core
		if($package_group->core==1)
		{
			return false;
		}

		$to_update = [
			'deleted' 		  		=> date("Y-m-d H:i:s"),
		];
		return $this->update($id,$to_update);
	}

	private function resetDefaults()
	{
		//reset all
		$this->db->update('nct_packages_groups', ['default'=>0]);
	}

}