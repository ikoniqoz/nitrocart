<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class FlatratePerItem_ShippingMethod extends ViewObject {

	public $name = 'Flat Rate Per Item';
	public $description = 'Flat Rate Per Item';
	public $author = 'inspiredgroup.com.au';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';
	public $tax_rate = 0; //10%

	public $fields = array(
		array(
			'field' => 'options[amount]',
			'label' => 'Shipping Charge Per Item',
			'rules' => 'trim|max_length[5]|is_numeric'
		),
		array(
			'field' => 'options[handling]',
			'label' => 'Handling',
			'rules' => 'trim|max_length[5]|is_numeric'
		),
		array(
			'field' => 'usepackages',
			'label' => 'Use Package or Items',
			'rules' => 'trim|numeric',
		),		
	);


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
	
	
	public function __construct() 
	{	

	}

	public function calc( $options, $items, $to_address = [] )
	{
		$shippable = new ViewObject();
		$shippable->cost = $this->_calc( $options, $items, $to_address );
		$shippable->tax  = $shippable->cost * $this->tax_rate;
		//$shippable->packing_slip  = $this->_packing_slip;

		return $shippable;
	}

	private function _calc( $options, $items, $to_address = [] )
	{

		/**
		 * In the options we store the multiplier
		 * @var [type]
		 */
		$pi  = floatval($options['amount']);


		/**
		 * 
		 */
		$shippable_items = $this->doSorty($options,$items);

		/**
		 * Set the cost to the default handling
		 * @var [type]
		 */
		$cost = floatval($options['handling']);


		/**
		 * We count the shippable_items multiply by the amount per item
		 */
		//foreach ($shippable_items as $item)
		//{
		//	$cost += $pi;
		//}
		$cost += ( count($shippable_items) * $pi );


		/**
		 * Then simply return the total cost
		 */
		return $cost;
	}


	private function doSorty($options,$items)
	{
		
		$this->load->library('nitrocart/packages_library');

		// 
		// Define shippable boxes
		//
		$_shippable_boxes = [];


		// 
		// Packages OR items
		//
		if($options['usepackages'] == 'packages')
		{
			$this->packages_library->pack( $items );
			$_shippable_boxes = $this->packages_library->getShippableContainers();
			$this->_packing_slip = $this->packages_library->getTrace();
		}
		else
		{
			$_shippable_boxes = $this->packages_library->getShippableContainersCartItems($items);
			$this->_packing_slip = 'Packaging System Not used.';
		}	

		return $_shippable_boxes;	
	}

}


 