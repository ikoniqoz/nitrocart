<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Shipping_options_m extends MY_Model {

	public $_table = 'nct_checkout_options';
	public $_type = 'shipping'; //shipping|gateway

	public function __construct()
	{
		parent::__construct();
	}

	public function install($slug,$name)
	{
		//prepare array
		$insert = array(
			'title' 		=> $name,
			'slug' 			=> $slug,
			'description' 	=> '',
			'enabled' 		=> 0,
			'options' 		=> serialize(''), 
            'created_by'    => $this->current_user->id,
            'created'       => date("Y-m-d H:i:s"),
            'updated'       => date("Y-m-d H:i:s"),
            'module_type' 	=> $this->_type,
		);

		return $this->insert($insert); 
	}	

	public function uninstall($id)
	{
		$edit['enabled'] = 0;
		$edit['deleted'] = date("Y-m-d H:i:s");
		return $this->update($id, $edit);
	}

	public function enable($id)
	{
		$edit['enabled'] = 1;
		return $this->update($id, $edit);
	}

	public function disable($id)
	{
		$edit['enabled'] = 0;
		return $this->update($id, $edit);
	}


	public function edit($input)
	{

		if(isset($input['id']))
		{
			$edit = [];
			$edit['title'] 			= $input['title'];
			$edit['description'] 	= $input['description'];
			$edit['options'] 		= (isset($input['options'])) ? serialize($input['options']) : serialize("") ; 
			$edit['enabled'] 		= (isset($input['enabled'])) ? $input['enabled'] : 0 ;

			return $this->update( (int) $input['id'], $edit );
		}

		return false;
	}

	public function delete($id)
	{
		$edit = [];
		$edit['enabled'] = 0;
		$edit['deleted'] = date("Y-m-d H:i:s");

		return $this->update($id, $edit);
	}	

	public function get_all()
	{
		return $this->db->where('module_type',$this->_type)->where('deleted',NULL)->get($this->_table)->result();
	}

	public function get($id)
	{
		$co = parent::get($id);
		$co->options = unserialize($co->options);
		return $co;
	}


}