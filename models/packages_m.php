<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Packages_m extends MY_Model
{

	public $_table = 'nct_packages';

	public $_create_validation_rules = [
			[
				'field' => 'name',
				'label' => 'lang:name',
				'rules' => 'trim|max_length[100]|required'
			],
			[
				'field' => 'code',
				'label' => 'Code',
				'rules' => 'trim|max_length[100]'
			],			
			[
				'field' => 'pkg_group_id',
				'label' => 'Package Group',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'height',
				'label' => 'Inner Height',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'width',
				'label' => 'Inner Width',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'length',
				'label' => 'Inner Length',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'outer_height',
				'label' => 'Outer Height',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'outer_width',
				'label' => 'Outer Width',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'outer_length',
				'label' => 'Outer Length',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'max_weight',
				'label' => 'Max Weight',
				'rules' => 'trim|numeric|required'
			],
			[
				'field' => 'cur_weight',
				'label' => 'Package Weight',
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

	public function selectPackagesByGroup($package_group_id)
	{
		return $this->where('deleted',NULL)->where('pkg_group_id',$package_group_id)->get_all();
	}

}