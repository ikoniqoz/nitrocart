<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Wishlist_m extends MY_Model
{

	/**
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'nct_wishlist';
		$this->primary_key = 'product_id';
	}

	/**
	 * @param $product
	 * @param $user_id
	 * @access public
	 */
	public function add($user_id, $product)
	{
		$input['user_id'] = $user_id;
		$input['product_id'] = $product->id;
		$input['price'] = $product->price;
		$input['date_added'] = date("Y-m-d H:i:s");
		$input['user_notified'] = 0;

		return $this->insert($input);
	}

	/**
	 * @param $product -
	 * @param $user_id
	 * @access public
	 *
	 * Note: changed to remove so that it doesnt conflict with MY_Model
	 */
	public function remove($user_id, $product_id)
	{
		$data = ['user_id' => $user_id, 'product_id' => $product_id];
		if ($this->delete_by($data))
		{
			return true;
		}
		return false;
	}

	/**
	 * Get all wishlist items
	 * @return [type] [description]
	 */
	public function get_all()
	{
		$this->db->select('nct_products.*,nct_wishlist.price');
		$this->db->join('nct_products', 'nct_products.id = nct_wishlist.product_id', 'inner');
		return parent::get_all();
	}


	/**
	 * Checks wheather the item is already in the customers wishlist
	 *
	 * @param
	 * @param
	 * @access public
	 */
	public function item_exist($user_id = 0, $product_id = 0)
	{
		$data = ['user_id' => $user_id, 'product_id' => $product_id];
		if ($this->wishlist_m->get_by($data))
		{
			return true;
		}
		return false;
	}
}