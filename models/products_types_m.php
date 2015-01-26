<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Products_types_m extends MY_Model
{

	public $_table = 'nct_products_types';

	protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';

    public $validation_rules = [
            [
                    'field' => 'name',
                    'label' => 'Name',
                    'rules' => 'required|trim|callback__validatename[]'
            ],
    ];

	public function __construct()
	{
		parent::__construct();
	}


	public function get_all_types()
	{
		return $this->get_all();
	}

	public function getDefaultID()
	{
		$row = $this->db->where('default',1)->get('nct_products_types')->row();
		if($row)
		{
			return $row->id;
		}
		return 0;
	}

	private function getReturnObject()
	{
		$obj = [];
		$obj['status'] 	= false;
		$obj['message'] = 'No parameters set.';
		return $obj;
	}


}