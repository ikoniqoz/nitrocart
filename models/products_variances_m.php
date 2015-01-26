<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Products_variances_m extends MY_Model
{
	public $_table = 'nct_products_variances';
	protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';
	public function __construct()
	{
		parent::__construct();
	}
	public function get_by_product($product_id)
	{
		return $this->where('product_id',$product_id)->where('deleted',NULL)->get_all();
	}
	public function get_by_product_front($product_id)
	{
		return $this->where('product_id',$product_id)->where('available',1)->where('deleted',NULL)->get_all();
	}
	/**
	 * Restrict User from accessing CUD functions
	 */
	public function delete($id)
	{
		return false;
	}
}