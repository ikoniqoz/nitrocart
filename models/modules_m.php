<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Modules_m extends MY_Model {


	public $_table = 'nct_modules';

	public function __construct()
	{
		parent::__construct();
	}


	/**
	 *
	 * Array data Expected :
	 *   array('name'=>'Categories' , 'namespace'=>'shop_categries','prodtab',true|false)
	 */
	public function install($data=[])
	{

		$namespace = $data['namespace'];

		$to_insert = [
				'name' 			 => $data['name'],
				'namespace' 	 => $data['namespace'],
				'path' 			 => (isset($data['path']))?$data['path']:'',
				'driver' 		 => (isset($data['driver']))?$data['driver']:'',
				'prod_tab_order' => (int) $data['prod_tab_order'],
				'core'			 => (trim($data['namespace'])=='nitrocart')?1:0,
				'type'			 => (trim($data['namespace'])=='nitrocart')? ((trim($data['path'])=='systems')?'system':'feature'):'extension',
				'created' 		 => date("Y-m-d H:i:s"),
				'updated' 		 => date("Y-m-d H:i:s"),
				'created_by' 	 => $this->current_user->id,
				'ordering_count' => 0,
		];


		if(isset($data['routes']))
		{
			$this->load->model('nitrocart/admin/routes_admin_m');

			foreach($data['routes'] as $route)
			{
				$this->routes_admin_m->create($route,$namespace);
			}
		}

		return $this->insert($to_insert); //returns id
	}

	/**
	 *
	 * Array data Expected :
	 *   array('name'=>'Categories' , 'namespace'=>'shop_categries','prodtab',true|false)
	 */
	public function uninstall($data=[])
	{

		$result = $this->db->where('namespace',$data['namespace'])->from('nct_modules')->delete();

		if(isset($data['routes']))
		{
			$this->load->model('nitrocart/admin/routes_admin_m');

			foreach($data['routes'] as $route)
			{
				$this->routes_admin_m->remove_route($route['uri']);
			}
		}
		return $result;
	}

	/*all installed*/
	public function get_installed()
	{
		$modules =  $this->order_by('prod_tab_order')->get_all();

		return $this->setupReturnObject( $modules );
	}

	public function get_cart_modules()
	{
		//get all where the module wants to be called for processing cart
		$modules =  $this->where('cart',1)->get_all();

		return $this->setupReturnObject( $modules );
	}

	public function get_prod_tab_modules()
	{
		$modules = $this->where('prod_tab',1)->order_by('prod_tab_order')->get_all();
		return $this->setupReturnObject( $modules );
	}


	public function get_by_name($name)
	{
		return $this->where('name',$name)->get_all();
	}


	private function setupReturnObject($modules=[])
	{
		foreach($modules AS $key=>$value)
		{
			$value->libpath = strtolower($value->namespace.'/'. $value->namespace.'_integration_library');
			$modules[$key] = $value;
		}
		return $modules;
	}
}