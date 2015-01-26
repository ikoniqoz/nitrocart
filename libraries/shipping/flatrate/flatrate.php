<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Flatrate_ShippingMethod {

	public $name =  'Flatrate';
	public $description = 'Flatrate for All Items';
	public $author = 'inspiredgroup.com.au';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';
	public $tax_rate = 0.1; //10%

	public $fields = array(
		array(
			'field' => 'options[amount]',
			'label' => 'Amount for shipping',
			'rules' => 'trim|max_length[5]|is_numeric'
		),
		array(
			'field' => 'options[tax_rate]',
			'label' => 'The TAX rate to calculte shipping. Note that Tax is TI-Tax Inclusive.',
			'rules' => 'trim|max_length[5]|is_numeric'
		),		
	);


	public function __construct() {		}


	/**
	 * format the input before storing in db
	 * 
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	public function pre_save($input)
	{
		return $input;
	}

	/**
	 * Called just before edit view. pre-output formats data and makes sure default
	 * variables are available for display
	 * It also has access to the installed db data so it can format i required.
	 * 
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function pre_output()
	{
		//we have access to the options like so
		//var_dump($this->options['amount']);

		//we must return an array of any data we want.
		//if we dont need data we rreturn empty array
		$this->form_data = [];
	}

	public function calc( $options, $items, $to_address = [] )
	{
		$shippable = new ViewObject();
		$shippable->cost = $this->_calc( $options, $items, $to_address );
		$shippable->tax  = $shippable->cost * $options['tax_rate'];
		//$shippable->packing_slip = 'Packaging System not used.';
		return $shippable;
	}

	private function _calc( $options, $items, $to_address = [] )
	{
		if(isset($options['amount']))
			return (float) $options['amount'];
		return 0;
	}

}
