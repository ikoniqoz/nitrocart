<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Carts_m extends MY_Model
{

	/**
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'nct_carts';
	}

	public function get_all_by_user($user_id)
	{
		return $this->db->where('user_id',$user_id)->get('nct_carts')->result();
	}

	public function clear_all()
	{
		if($this->current_user)
		{
			$this->db->where('user_id',$this->current_user->id)->delete('nct_carts');
		}
		return true;
	}

	/**
	 * Single access to add/modify and remove
	 * 
	 * @param  [type] $user_id     [description]
	 * @param  [type] $variance_id [description]
	 * @param  [type] $product_id  [description]
	 * @param  [type] $price       [description]
	 * @param  [type] $qty         [description]
	 * @param  string $method      [description]
	 * @return [type]              [description]
	 */
	public function modify($user_id, $variance_id, $product_id, $price, $qty, $options=NULL)
	{
		if($id = $this->cart_item_exist($user_id, $variance_id, $product_id,$options))
		{
			return $this->_update($id, $user_id, $variance_id, $product_id, $price, $qty, $options);
		}
		else
		{
			return $this->_insert( $user_id, $variance_id, $product_id, $price, $qty, $options);
		}
	}

	public function has_items($user_id)
	{
		$row = $this->db->where('user_id',$user_id)->get('nct_carts')->row();
		return ($row)? true : false ;
	}


	private function _update($id, $user_id, $variance_id, $product_id, $price, $qty, $options=NULL)
	{
		$input = [];
		$input['user_id'] = $user_id;
		$input['variance_id'] = $variance_id; //varance id		
		$input['product_id'] = $product_id;
		$input['price'] = $price;
		$input['qty'] = $qty;		
		$input['options'] = json_encode($options);		
		$input['date'] = date("Y-m-d H:i:s");
		$input['session'] = session_id(); //always update ses id	
		return $this->db->where('id',$id)->update('nct_carts',$input);
	}

	private function _insert( $user_id, $variance_id, $product_id, $price, $qty, $options=NULL)
	{
		$input = [];
		$input['user_id'] = $user_id;
		$input['variance_id'] = $variance_id; //varance id		
		$input['product_id'] = $product_id;
		$input['price'] = $price;
		$input['qty'] = $qty;	
		$input['options'] = json_encode($options);	
		$input['date'] = date("Y-m-d H:i:s");	
		$input['session'] = session_id(); //always update ses id			
		return $this->db->insert('nct_carts',$input);
	}

	private function cart_item_exist($user_id, $variance_id, $product_id, $options)
	{
		$options = json_encode($options);
		$row = $this->db->where('user_id',$user_id)->where('variance_id',$variance_id)->where('product_id',$product_id)->where('options',$options)->get('nct_carts')->row();
		return ($row)?$row->id:NULL;
	}	
	
    public function destroy($user_id)
    {
    	if($this->db->table_exists('nct_carts'))
    	{
    		$this->db->where('user_id',$user_id)->delete('nct_carts');
    	}
    	return true;
    }
}