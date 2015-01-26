<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Comments_m extends MY_Model
{

    public $_table = 'nct_products_comments';
    
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'nct_products_comments';
	}

	public function create($data=[])
	{
		$to_insert = [
				'product_id' 	=> $data['product_id'],
				'user_id' 	 	=> $this->current_user->id,
				'flagged' 		=> 0,
				'visible' 		=> 1,
				'comment' 		=> $data['comment'],
				'date_comment'  => date("Y-m-d H:i:s"),
				'reffered'		=> $data['reffered'],
		];

		return $this->insert($to_insert); //returns id
	}


	public function flag($comment_id)
	{
		return $this->update($comment_id, ['flagged'=>1]); 
	}	

	public function get($id)
	{
		$comment = parent::get($id);
		if($review)
		{
			if($comment->visible==1)
			{
				return $comment;
			}
		}

		return NULL;
	}

	public function get_all()
	{
		return parent::select('*,id as comment_id')->where('visible',1)->get_all();
	}

}