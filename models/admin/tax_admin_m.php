<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/../tax_m.php');
class Tax_admin_m extends Tax_m {


	//public $_table = 'nct_tax';

	public function __construct()
	{
		parent::__construct();

	}

	public function getDefaultID()
	{
		$row = $this->db->where('default',1)->get('nct_tax')->row();

		if($row)
		{
			return $row->id;
		}

		return 0;
	}

	private function resetDefaults()
	{
		//reset all
		$this->db->update('nct_tax', array('default'=>0));
	}



	public function create($input)
	{

		if($input['default'] == 1)
		{
			$this->resetDefaults();
		}

		$to_insert 		= [
			'name' 		=> $input['name'],
			'rate' 		=> $input['rate'],
			'default'	=> $input['default'],
            'created_by'=> $this->current_user->id,
            'created'   => date("Y-m-d H:i:s"),
            'updated'   => date("Y-m-d H:i:s"),
		];

		return $this->insert($to_insert);

	}

	public function edit($id, $input)
	{

		if($input['default'] == 1)
		{
			$this->resetDefaults();
		}
		
		$update_record 	= [
			'name' 		=> $input['name'],
			'rate' 		=> $input['rate'],
			'default'	=> $input['default'],
            'updated'   => date("Y-m-d H:i:s"),
		];

		return $this->update($id, $update_record);

	}

	public function delete($id)
	{
		return $this->_setField($id,'deleted', date("Y-m-d H:i:s"));
	}

	private function _setField($id, $field_name, $value)
	{
		$update_record 	= [
            $field_name   => $value,
		];

		return $this->update($id, $update_record);
	}

}
