<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Routes_admin_m extends MY_Model {

	public $_table = 'nct_routes';

	public function __construct()
	{
		parent::__construct();
	}


	public function create($input,$namespace)
	{
		//var_dump($input);

		$this->db->trans_start();

		$to_insert = array(
				'name' 			=> $input['name'],
				'uri' 			=> NC_ROUTE . $input['uri'],
				'default_uri' 	=> $input['uri'],
				'dest' 			=> $input['dest'], //we only need 1 destination
				'module' 		=> $namespace,
				'created' 		=> date("Y-m-d H:i:s"),
				'updated' 		=> date("Y-m-d H:i:s"),
		);
		//remove the route if it exist
		$this->remove_route($input['uri']);
		//now add the new route
		$id = $this->insert($to_insert);
		//ok we are done
		$this->db->trans_complete();

		return ($this->db->trans_status() === false) ? false : $id;
	}

	public function remove_route($route_uri)
	{
		return $this->delete_by(array('default_uri'=>$route_uri));
	}

	public function remove_by_module($module)
	{
		/*
		$update_record = array(
			'deleted' =>  1,
		);

		return $this->where('user_id', $user_id)->update($address_id, $update_record); //returns id
		*/
		return true;
	}

	public function do_truncate()
	{
		$this->db->truncate('nct_routes');
		return true;
	}

}