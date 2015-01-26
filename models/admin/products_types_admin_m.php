<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/../products_types_m.php');
class Products_types_admin_m extends products_types_m
{

	//public $_table = 'nct_products_types';
	//protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';
    //public $validation_rules = array(

	public function __construct()
	{
		parent::__construct();
        $this->load->helper('nitrocart/nitrocart_admin');
	}


	/**
	 * This is exposed to admin/user
	 */
	public function create($input, $props=[], $core=0 )
	{
		return $this->_create($input, $props, $core );
	}

	/**
	 * this function creates the type
	 */
	private function _create($input, $props=[], $core=0 )
	{
		$name = $this->get_unique_name( $input['name'] );
		$slug = $this->get_unique_slug( $name );

		if($input['default'] == 1)
		{
			$this->resetDefaults();
		}

		$to_insert = [];
		$to_insert['name']		  	= $name;
		$to_insert['created_by']   	= $this->current_user->id;
        $to_insert['created']     	= date("Y-m-d H:i:s");
        $to_insert['updated']       = date("Y-m-d H:i:s");
        $to_insert['slug']    		= $slug;
        $to_insert['default']		= $input['default'];
        $to_insert['properties'] 	= serialize( $props ); //serialize( []);
        $to_insert['core'] 			= $core;


		$id = $this->insert($to_insert);
		return $id;
	}	

	public function wipe_props($id)
	{
		return $this->update($id, array('properties'=> serialize( [] ) ) );
	}

	public function edit($id, $input, $replicate_to_products=false)
	{

		$name = $this->get_unique_name( $input['name'] , $id);
		$slug = $this->get_unique_slug( $name , $id);
		
		if($input['default'] == 1)
		{
			$this->resetDefaults();
		}

		$the_update = [];
		$the_update['name']		  	= $name;
        $the_update['updated']       = date("Y-m-d H:i:s");
        $the_update['slug']    		= $slug;
        $the_update['default']		= $input['default'];
        $the_update['properties'] 	= serialize( $input['properties'] );

		$status = $this->update($id, $the_update );

		//now update all products with the new slug
		$this->db->where('type_id',$id)->update('nct_products', array('type_slug'=>$slug));

		return true;
	}


	/**
	 * Need to implement some checks to see if the type can be deleted
	 * 1. That no existing product is using it.
	 * 2. That this is not a core type
	 */
	public function delete($id)
	{

		$type = $this->get($id);
		
		if($type->core==1)
		{
			return false;
		}


		//check # 1
		$result = $this->db->where('type_id',$id)->get('nct_products')->result();

		if( count($result) > 0)
		{
			//cant delete becuase active products are using this
			return false;
		}

		parent::delete( $id );
		
		return true;
		//can not delete a type of a product is or has used it.
		// can be renamed tho!
	}



	/**
	 * prepare the array so it can be used as a dropdown
	 */
	public function get_for_admin()
	{

		$return_array = [];
		$r = $this->get_all();

		foreach($r as $key=>$value)
		{
			$return_array[$value->id]=$value->name;
		}

		return $return_array;

	}

	public function deserialize_properties($props=[])
	{
		$a = [];
		$props = unserialize($props);
		foreach($props as $p)
		{
			$a[] = $p->id; 
		}
		return $a;
	}


    protected function get_unique_name($name, $id = -1, $prefix = '')
    {
        // 1
        $name = (trim($name) == "") ? $prefix.$name : $name ;

        // 2
        $slug_count = $this->db->where('id !=',$id)->where('name', $name )->get( $this->_table )->num_rows();

        //3.
        return ($slug_count > 0) ? $this->get_unique_slug(  ($name.'-'.$slug_count)  , $id, $prefix) :  $name;
    }

    protected function get_unique_slug($slug, $id = -1, $prefix = '')
    {
        // 1
        $slug = (trim($slug) == "") ? $prefix.$slug : $slug ;

        // 2
        $slug = shop_slugify($slug);

        // 3
        $slug_count = $this->db->where('id !=',$id)->where('slug', $slug )->get( $this->_table )->num_rows();

        //4.
        return ($slug_count > 0) ? $this->get_unique_slug(  ($slug.'-'.$slug_count)  , $id, $prefix) :  $slug;
    }

	private function resetDefaults()
	{
		//reset all
		$this->db->update('nct_products_types', array('default'=>0));
	}    



}