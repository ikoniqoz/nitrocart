<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Attributes_m extends MY_Model
{

	public $_table = 'nct_attributes';

	protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';

    public $validation_rules = array(
            array(
                    'field' => 'name',
                    'label' => 'Name',
                    'rules' => 'required|trim|callback__validatename[]'
            ),
    );

	public function __construct()
	{
		parent::__construct();
	}

	public function get_name($id)
	{
		$o = $this->get($id);
		if($o)
		{
			return $o->name;
		}
		return 'oops - something went wrong!';
	}

	public function create($input)
	{
		$name = $this->get_unique_name( $input['name'] );
		$slug = $this->get_unique_slug( $name );

		$to_insert = [
				'name' 		  	=> $name,
                'slug'       	=> $slug,
		];
		$id = $this->insert($to_insert);
		return $id;
	}

	public function get_dropdown_array()
	{
		$a = $this->get_all();
		$na = [];
		foreach($a as $k)
		{
			$na[$k->id]= $k->name;
		}
		return $na;
	}

	public function edit($input)
	{

		$name = $this->get_unique_name( $input['name'] ,  $input['id'] );
		//$slug = $this->get_unique_slug( $name , $id);

		$the_update = array(
			'name' 			=> $name,
			//'slug' 		=> $slug,
        );


		$status = $this->update($input['id'] , $the_update );

		return true;
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

	private function getReturnObject()
	{
		$obj = [];
		$obj['status'] 	= false;
		$obj['message'] = 'No parameters set.';
		return $obj;
	}

}