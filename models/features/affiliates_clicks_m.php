<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Affiliates_clicks_m extends MY_Model
{

	public $_table = 'nct_affiliates_clicks';

	protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';

	public function __construct()
	{
		parent::__construct();
	}

	public function log($aff_id,$page='')
	{

		$to_insert = array(
				'affiliate_id' => $aff_id,
				'page' => $page,
				'ip_address' => $this->session->userdata('ip_address'),
				'date' => date("Y-m-d 00:00:00"), //time must be set to ZERo so we can identify a time cut off for the day
		);

		$id = $this->insert($to_insert);

		if($id)
		{
			return $id;
		}

		return -1;
	}


	public function exist($aff_id,$page='')
	{
		$items = $this
				->where('affiliate_id',$aff_id)
				->where('page',$page)
				->where('ip_address',$this->session->userdata('ip_address'))
				->where('date',date("Y-m-d 00:00:00"))
				->limit(1)
				->get_all();

		return (count($items))?true:false;
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

}