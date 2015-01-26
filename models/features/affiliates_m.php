<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Affiliates_m extends MY_Model
{

	public $_table = 'nct_affiliates';

	protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';

	public function __construct()
	{
		parent::__construct();
	}


	public function create($input)
	{

		$to_insert = array(
				'name' 		  => $input['name'],
				'code' 		  => md5( $input['name'] . '-' . time() ) . '-' . 'A' . rand(100,200) ,
		);

		$id = $this->insert($to_insert);

		if($id)
		{
			return $id;
		}

		return -1;
	}

	public function get_all_types()
	{
		return $this->where('deleted',NULL)->get_all();
	}


	private function getReturnObject()
	{
		$obj = array();
		$obj['status'] = false;
		$obj['message'] = 'No parameters set.';

		return $obj;
	}

	/**
	 * prepare the array so it can be used as a dropdown
	 */
	public function get_for_admin()
	{
		$return_array = array();
		$r = $this->where('deleted',NULL)->get_all();

		foreach($r as $key=>$value)
		{
			$return_array[$value->id]=$value->name;
		}

		return $return_array;

	}

	public function get_for_admin_2()
	{
		$return_array = array();
		$r = $this->where('deleted',NULL)->get_all();

		foreach($r as $key=>$value)
		{
			$return_array[$value->code]=$value->name;
		}

		return $return_array;

	}	

}