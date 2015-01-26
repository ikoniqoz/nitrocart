<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Workflows_m extends MY_Model
{

    public $_table = 'nct_workflows';

	public	$_create_validation_rules = [
			[
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'trim|required|max_length[100]'
			],
	];

	public	$_edit_validation_rules = [
			[
				'field' => 'name',
				'label' => 'Name',
				'rules' => 'trim|required|max_length[100]'
			],
	];

	public function __construct()
	{
		parent::__construct();

	}


	public function create($input)
	{

		$this->load->helper('nitrocart_admin');


		$to_insert = [
			'name' => strip_tags($input['name']),
			'pcent' => 10,
		];

		$id = $this->insert($to_insert);

		if(isset($input['is_placed']) AND $input['is_placed']==1)
		{
			$this->update_placed_all($id);
		}

		return $id;

	}

	private function update_placed_all($id)
	{
		$this->db->update($this->_table, ['is_placed'=>0]);
		$this->db->where('id',$id)->update($this->_table, ['is_placed'=>1]);
	}

	/**
	 *
	 * @return INT id of the updated row for success
	 * @access public
	 */
	public function edit($id, $input) {
		$to_update = [
			'name' => strip_tags($input['name']),
			'pcent' => $input['pcent'],
		];
		if(isset($input['is_placed']) AND $input['is_placed']==1)
		{
			$this->update_placed_all($id);
		}
		return $this->update($id, $to_update);
	}

	public function form_select( $return_array=[] ,$sort_percentage=false ) {
		// selecting the parents
		$workflows = $this->order_by('pcent','asc')->get_all();
		foreach($workflows as $item)
		{
			$return_array[$item->id] = $item->name . ' ( ' . $item->pcent . ' % )';
		}
		return $return_array;
	}
}