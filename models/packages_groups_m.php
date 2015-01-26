<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Packages_groups_m extends MY_Model
{

	public $_table = 'nct_packages_groups';

	public $_create_validation_rules = [
			[
				'field' => 'name',
				'label' => 'lang:name',
				'rules' => 'trim|max_length[100]|required'
			],
			[
				'field' => 'default',
				'label' => 'lang:default',
				'rules' => 'trim|numeric|required'
			],			
	];

	public function __construct()
	{
		parent::__construct();
	}



	public function get_all_available()
	{
		return $this->where('deleted',NULL)->get_all();
	}

	/**
	 * can not do this in public model
	 */
	public function delete($id)
	{
		return false;
	}


	public function getDefaultID()
	{
		$row = $this->db->where('default',1)->get('nct_packages_groups')->row();
		if($row)
		{
			return $row->id;
		}
		return 0;
	}
}