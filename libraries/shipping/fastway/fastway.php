<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

require_once( dirname(__FILE__) . '/fastway_core.php');

class Fastway_ShippingMethod extends Fastway_core
{


	public function __construct()
	{
		parent::__construct();		
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
		$data = [];
		$data['RFCODES'] = $this->getFranchiseForOZ($this->options['apikey']);

		if(count($data['RFCODES'] ) ==0)
		{
			$data['RFCODES'] = array('SYD'=>'Sydney');
		}

		$data['PACKAGE_TYPES'] = array(
				'Parcel'=>'Parcel',
				'Satchel'=>'Satchel',
				'Either'=>'Either (First Served)',
		);

		$data['KEEP_RESPONSES'] = array(
				0=>'No Thanks',
				1=>'Yes Please',
		);

		$data['MULTI_REGIONS'] = array(
				'true'=>'Yes | True',
				'false'=>'No | False',
		);

		$this->form_data = $data;
	}	

	public function calc( $options, $items, $to_address = array() )
	{
		$shippable = new ViewObject();
		$shippable->cost = $this->_calc( $options, $items, $to_address );
		$shippable->tax  = $shippable->cost  * $this->tax_rate;
		$shippable->packing_slip = $this->_packing_slip;
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
		//$this->setKey($options['apikey']);


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
			$this->_packing_slip = 'Packaging System not used.';
		}
		//var_dump($_shippable_boxes);;



		$suburb = '';

		$ret_cost = 0;



		//only calc if items
		if(count($_shippable_boxes)>0)
			$ret_cost =  $this->doCalc( $options, $_shippable_boxes, $to_address->zip, $suburb,  $this->packages_library->getRedirOptions() );


		//add the handling
		if(isset( $options['handling']))
		{
			$ret_cost += $options['handling'];
		}


		if(isset( $options['mincharge']))
		{
			if( $options['mincharge'] > $ret_cost )
			{
				$ret_cost = $options['mincharge'];
			}
		}


		//fix max
		if(isset( $options['maxcharge']))
		{
			if( $options['maxcharge'] > 0 )
			{
				if( $options['maxcharge'] > $ret_cost )
				{
					$ret_cost = $options['maxcharge'];
				}
			}
		}

		
		// Return the cost
		return $ret_cost;
	}




}