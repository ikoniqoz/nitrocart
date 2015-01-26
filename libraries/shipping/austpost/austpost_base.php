<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
include_once('austpost_core.php');

class AustPostBase extends AustPostCore
{

	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Same key params as parent
	 *
	 * @param  [type] $options    [description]
	 * @param  [type] $items      [description]
	 * @param  array  $to_address [description]
	 * @return [type]             [description]
	 */
	public function calcDomesticParcel( $options, $items, $zip=3000, $delivery_option = AustPostServiceCode::AUS_PARCEL_REGULAR )
	{

		$dist_zip = (isset($options['distcode']))?$options['distcode']:3000;
		$extra_cover_amount = (isset($options['extracover']))?$options['extracover']:0;

		//$this->load->model('nitrocart/products_variances_m');
		//$this->load->model('nitrocart/packages_m');

		$_total_cost = 0;
		$__count = 0;

		foreach($items as $item)
		{

			$__count++;


			$app = new APUrlParam($dist_zip,$zip);


			//
			//item must have the array items in keys
			//
			$app->addBox($item);


			//
			// Set the delivery type (regular/standard/express)
			//
			$app->setDeliveryMethod( $delivery_option );



			//$app->add('option_code', AustPostServiceOption::AUS_SERVICE_OPTION_STANDARD);


			//
			//we must add sig on del if over 300
			//
		    if( $extra_cover_amount > 300.00)
		    {
		    	$app->add('option_code', AustPostServiceOption::AUS_SERVICE_OPTION_SIGNATURE_ON_DELIVERY );
		    }


			//
			// Is there extra cover
			if($extra_cover_amount > 0 )
			{
				$app->add('suboption_code', AustPostServiceOption::AUS_SERVICE_OPTION_EXTRA_COVER);
				$app->add('extra_cover', $extra_cover_amount);
			}

			// Make the call
	        try
	        {
		        $_total_cost += ($item['qty'] * $this->callAPService( $app->getDomesticParcelURI() ) );
	        }
	        catch (Exception $e)
	        {
	            $msg = "oops: ".$e->getMessage();
	            $this->set_flashdata(JSONStatus::Error, $msg);
	            redirect(NC_ROUTE);
	        }

	        //clear
	        $app = NULL;

		}

		//return $__count;
		return $_total_cost;
	}


	public function calcDomesticLetter( $dist_zip, $items, $to_address = array() )
	{
		return false;
	}


}
/* End of file austpost_base.php */
/* Location: ./application/models/austpost_base.php */