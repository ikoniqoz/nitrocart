<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Carts_admin_m extends MY_Model
{
    public $_table = 'nct_carts';

    public function __construct()
    {
        parent::__construct();
    }


    public function count_carts()
    {
    	return $this->db->group_by('user_id')->get('nct_carts')->num_rows();
    }

    public function get_carts()
    {
    	$query = $this->db->select('nct_carts.user_id,users.username')
				->select_sum('price','value')
				->select_sum('qty')
				->select_max('date')
				->select_min('date','oldest')
    			->from('users')
		    	->join('nct_carts','nct_carts.user_id = users.id')
		    	->group_by('user_id')
		    	->order_by('date','desc')
		    	->get()->result();

		return $query;
    }
    public function get_cart($user_id)
    {
        $query = $this->db->select('nct_products.name,nct_carts.*')
                ->where('user_id',$user_id)
                ->from('nct_products')
                ->join('nct_carts','nct_carts.product_id = nct_products.id')
                ->order_by('date','desc')
                ->get()->result();

        return $query;
    }

    public function delete($user_id)
    {
    	$this->db->where('user_id',$user_id)->delete('nct_carts');
    	return true;
    }
}