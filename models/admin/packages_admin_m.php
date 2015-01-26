<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/../packages_m.php');
class Packages_admin_m extends Packages_m
{

	public function __construct()
	{
		parent::__construct();
	}

	public function create($input,$core=0)
	{
		$to_insert = 
		[
			'name' 		  		=> $input['name'],
			'code' 		  		=> isset($input['code'])?$input['code']:'',
			'pkg_group_id' 		=> $input['pkg_group_id'],
			'height' 		  	=> $input['height'],
			'width' 		  	=> $input['width'],
			'length' 		  	=> $input['length'],
			'outer_height' 		=> $input['outer_height'],
			'outer_width' 		=> $input['outer_width'],
			'outer_length' 		=> $input['outer_length'],
			'max_weight' 		=> $input['max_weight'],
			'cur_weight' 		=> $input['cur_weight'],
            'created_by'    	=> $this->current_user->id,
            'created'       	=> date("Y-m-d H:i:s"),
            'updated'       	=> date("Y-m-d H:i:s"),
			'deleted' 		  	=> NULL,
			'core'				=> $core
		];

		$id = $this->insert($to_insert);

		return ($id) ? $id : -1;

	}

	public function save($id, $input)
	{
		$to_insert = 
		[
			'name' 		  		=> $input['name'],
			'code' 		  		=> isset($input['code'])?$input['code']:'',
			'pkg_group_id' 		=> $input['pkg_group_id'],
			'height' 		  	=> $input['height'],
			'width' 		  	=> $input['width'],
			'length' 		  	=> $input['length'],
			'outer_height' 		=> $input['outer_height'],
			'outer_width' 		=> $input['outer_width'],
			'outer_length' 		=> $input['outer_length'],
			'max_weight' 		=> $input['max_weight'],
			'cur_weight' 		=> $input['cur_weight'],
            'updated'       	=> date("Y-m-d H:i:s"),

		];

		return $this->update($id, $to_insert);
	}

	public function duplicate( $id  )
	{

		$row = $this->get($id);

		$n_name = $this->get_new_unique_name( $row->name );

		//create the input
		$to_insert = 
		[
			'name' 		  		=> $n_name,
			'code' 		  		=> $row->code,
			'pkg_group_id' 		=> $row->pkg_group_id,
			'height' 		  	=> $row->height,
			'width' 		  	=> $row->width,
			'length' 		  	=> $row->length,
			'outer_height' 		=> $row->outer_height,
			'outer_width' 		=> $row->outer_width,
			'outer_length' 		=> $row->outer_length,
			'max_weight' 		=> $row->max_weight,
			'cur_weight' 		=> $row->cur_weight,
            'created_by'    	=> $this->current_user->id,
            'created'       	=> date("Y-m-d H:i:s"),
            'updated'       	=> date("Y-m-d H:i:s"),
			'deleted' 		  	=> NULL,
			'core'				=> 0 /*even if 1, change to 0*/
		];

		//Add record
		return $this->insert($to_insert);

	}


    /**
     * should only be used for new slugs/duplicate
     * This is the prefered for new products and duplicate method
     */
    protected function get_new_unique_name( $name = '', $count = 0,$first=true)
    {

        $test_slug = ( $first==true ) ? $name : $name.'-'.$count ;


        $new_count = $this->db->where('name', $test_slug )->where('deleted',NULL)->get( $this->_table )->num_rows();

        if ( $new_count > 0 )
        {
            return $this->get_new_unique_name(  $name  , ($count+1) , false);
        }

        return $test_slug;
 
    }

	/**
	 * prepare the array so it can be used as a dropdown
	 */
	public function get_for_admin()
	{
		$return_array = [];
		$r = $this->where('deleted',NULL)->get_all();
		foreach($r as $key=>$value)
		{
			$return_array[$value->id]=$value->name;
		}
		return $return_array;
	}

	public function delete($id)
	{
		$ob = $this->get($id);
		if($ob)
		{
			if($ob->core==1)
			{
				return false;
			}

			$to_update = [
				'deleted' 	=> date("Y-m-d H:i:s"),
			];
			return $this->update($id, $to_update);
		}

		return false;

	}

}