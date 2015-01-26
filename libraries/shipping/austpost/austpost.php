<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
include_once('austpost_base.php');

class AustPost_ShippingMethod extends AustPostBase
{

	public function __construct()
	{
		parent::__construct();
	}


	public $name = 'Aust Postage';
	public $description = 'Aust Postage';
	public $author = 'inspiredgroup.com.au';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';
	public $tax_rate = 0.1; //10%


	public $fields = array(
		array(
			'field' => 'apikey',
			'label' => 'API Key',
			'rules' => 'trim|text',
		),
		array(
			'field' => 'distcode',
			'label' => 'Distribution PostCode',
			'rules' => 'trim|text',
		),
		array(
			'field' => 'extracover',
			'label' => 'Extra Cover',
			'rules' => 'trim|numeric',
		),
		array(
			'field' => 'usepackages',
			'label' => 'Use Package or Items',
			'rules' => 'trim|numeric',
		),
		array(
			'field' => 'deliveryoption',
			'label' => 'Select Delivery Option',
			'rules' => 'trim',
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
		$this->form_data = array();
	}

	/**
	 * Calculate the shipping for the given options
	 * 
	 * @param  [type] $options    [description]
	 * @param  [type] $items      [description]
	 * @param  array  $to_address [description]
	 * @return [type]             [description]
	 */
	public function calc( $options, $items, $to_address = array() )
	{
		$shippable = new ViewObject();
		$shippable->cost = $this->_calc( $options, $items, $to_address );
		$shippable->tax  = $shippable->cost  * ($this->tax_rate / 100);
		$shippable->packing_slip  = $this->_packing_slip;

		return $shippable;
	}

	private function _calc( $options, $items, $to_address = array() )
	{

		$this->load->library('nitrocart/packages_library');

		//
		// Initialize the cost
		//
		$_shippable_boxes = array();

		//
		// Validate API before continuing
		//
		if(! $this->validateAPIKey($options) && $this->validateOptions())
		{
			//handle this and cancel
			$this->session->set_flashdata(JSONStatus::Error,'Unable to Validate AustPost API Key. Shipping for this order has not been calculated');
			return false;
		}

		//
		// Set the API KEY from settings
		//
		$this->setKey($options['apikey']);


		// Calc parcel domestic
		// ppackages OR items
		if($options['usepackages'] == 'packages')
		{
			$this->packages_library->pack( $items );
			$_shippable_boxes = $this->packages_library->getShippableContainers();
			$this->_packing_slip = $this->packages_library->getTrace();
			//var_dump($_shippable_boxes);die;
		}
		else
		{
			$_shippable_boxes = $this->packages_library->getShippableContainersCartItems($items);
			$this->_packing_slip = 'Packaging syste not used.';
		}
		//var_dump($_shippable_boxes);;

		switch ($options['deliveryoption'])
		{

			case 'sameday':
			case 'express':
				$delivery_option = AustPostServiceCode::AUS_PARCEL_EXPRESS;
				break;
			case 'regular':
			default:
				# code...
				$delivery_option = AustPostServiceCode::AUS_PARCEL_REGULAR;
				break;
		}

		//only calc if items
		if(count($_shippable_boxes)>0)
			return $this->calcDomesticParcel( $options, $_shippable_boxes, $to_address->zip, $delivery_option );

		// Return the cost
		return 0;
	}


	private function validateAPIKey($options)
	{
		if(!(isset($options['apikey'])))
		{
			return false;
		}

		if(trim($options['apikey'] ) == '')
		{
			return false;
		}

		//perhaps try to connect to AustPost for further validation!!

		return true;
	}

	private function validateOptions($options)
	{
		//perhaps try to connect to AustPost for further validation!!
		return true;
	}


}