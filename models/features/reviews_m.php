<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Reviews_m extends MY_Model
{

    public $_table = 'nct_products_reviews';
    
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'nct_products_reviews';
	}

	public function create($data=[])
	{

		$to_insert = [
				'product_id' 	=> $data['product_id'],
				'user_id' 	 	=> $this->current_user->id,
				'flagged' 		=> 0,
				'visible' 		=> 1,
				'rating' 		=> $data['rating'],
				'comment' 		=> $data['comment'],
				'date_reviewed' => date("Y-m-d H:i:s"),
				'reffered'		=> $data['reffered'],
				//'deleted'  		=> NULL,
				//'deleted_by' 	=> NULL,
		];

		return $this->insert($to_insert); //returns id
	}


	public function flag($comment_id)
	{
		return $this->update($comment_id, ['flagged'=>1]); 
	}	

	public function get($id)
	{
		$review = parent::get($id);
		if($review)
		{
			if($review->visible==1)
			{
				return $review;
			}
		}

		return NULL;
	}

	public function get_all()
	{
		return parent::select('*,id as comment_id')->where('visible',1)->get_all();
	}

}