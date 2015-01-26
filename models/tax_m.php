<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Tax_m extends MY_Model 
{

	public $_table = 'nct_tax';

	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Calculates the price based on
	 *
	 * @param Array Input
	 * @return The modifed array that contains the calculated values
	 */
	public function calc( $item )
	{
		return $this->calc_inclusive($item);
	}


	private function calc_inclusive( $item )
	{
		//if no tax then rweturn empty tax values
		if( $item['tax_id'] == NULL ) return $this->getNoTax();


		//find the tax record
		$tax_obj 		= $this->get($item['tax_id']);

		//calculate the rate
		$rate 		= ($tax_obj->rate>0)?($tax_obj->rate/100):0;

		//create NCfloat obejct
		$price 		= new NCFloat($item['price']);

		//calc the tax to pay (single)
		$sub_tax	= $price->taxinc($rate)->value();

		//calc the total, item * qty
		$tot_tax	= $item['qty'] * $sub_tax;


		$exc_total	= 0;


		//woot, done
		return [
				'rate'  => $tax_obj->rate,			
				'inc_sub'   => $sub_tax,
				'inc_total' => $tot_tax,
				'exc_sub' 	=> 0,
				'exc_total' => $exc_total,
				'total'		=> ($tot_tax + $exc_total)
			];
		
	}

	private function getNoTax()
	{
		return [
				'rate'  => 0,			
				'inc_sub'   => 0,
				'inc_total' => 0,
				'exc_sub' 	=> 0,
				'exc_total' => 0,
				'total'		=> 0
			];
	}


	public function get_admin_select()
	{
		$all = $this->where('deleted',NULL)->get_all();
		$r_array = [];
		//$r_array[NULL] = 'None';
		foreach($all  as $key=>$value) $r_array[$value->id] = $value->name;
		return $r_array;
	}

}