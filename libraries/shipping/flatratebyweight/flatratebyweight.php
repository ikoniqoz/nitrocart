<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class FlatrateByWeight_ShippingMethod {

	public $name = 'Flat Rate By Weight';
	public $description = 'Flat Rate By Weight';
	public $author = 'inspiredgroup.com.au';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';
	public $tax_rate = 0; //10%



	public $fields = array(
		array(
			'field' => 'options[amount]',
			'label' => 'Shipping Charge Per Kilo',
			'rules' => 'trim|max_length[5]|is_numeric'
		),
		array(
			'field' => 'options[handling]',
			'label' => 'Handling',
			'rules' => 'trim|max_length[5]|is_numeric'
		),	
	);

	public function __construct() 
	{	

	}

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
		$this->form_data = [];
	}
	




	public function calc( $options, $items, $to_address = array() )
	{
		$shippable = new ViewObject();
		$shippable->cost = $this->_calc( $options, $items, $to_address );
		$shippable->tax  = $shippable->cost  * $this->tax_rate;
		//$shippable->packing_slip  = 'Packaging System not used.';

		return $shippable;
	}

	private function _calc( $options, $items, $to_address = array() )
	{

		/**
		 * In the options we store the multiplier
		 * @var [type]
		 */
		$pk  = floatval($options['amount']);


		/**
		 * Set init weight
		 */
		$total_weight = 0;





		/**
		 * Set the cost to the default handling
		 * @var [type]
		 */
		$cost = floatval($options['handling']);



		/**
		 * add the weight
		 */
		foreach ($items as $item)
		{
			$total_weight += $item['weight'];
		}


		//now calc
		$cost += ( $total_weight * $pk );


		//check max

		//check min


		/**
		 * Then simply return the total cost
		 */
		return $cost;
	}

}
