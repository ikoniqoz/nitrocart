<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Coupons_m extends MY_Model
{

    public $_table = 'nct_coupons';
    
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'nct_coupons';
	}

	public function get_coupon($coupon_code)
	{
		$code = strtoupper(strip_tags(trim($coupon_code)));

		//create the default coupon
		$ret_coupon = [];
		$ret_coupon['code'] = '';
		$ret_coupon['pid'] = NULL;
		$ret_coupon['vid'] = NULL;
		$ret_coupon['rate'] = 0.0;

		if($row = $this->db->where('code',$code)->where('enabled',1)->where('max_use > used_count')->where('deleted', NULL )->get('nct_coupons')->row())
		{
			$ret_coupon['code'] = $row->code;
			$ret_coupon['pid'] = $row->product_id;
			//$ret_coupon['vid'] = $row->variance_id;
			$ret_coupon['rate'] = (float) ($row->pcent / 100);

		}

		return $ret_coupon;
	}

}