<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class AustPostCore
{

	protected $_api_key = 'XXXXXXXXXXXXXXXXX';


	const MAX_HEIGHT 	= 35; //only applies if same as width
	const MAX_WIDTH 	= 35; //only applies if same as height
	const MAX_WEIGHT 	= 20; //kgs
	const MAX_LENGTH 	= 105; //cms
	const MAX_GIRTH 	= 140; //cms
	const MIN_GIRTH 	= 16; //cms


    /**
     * Singleton
     *
     * @param  [type] $var [description]
     * @return [type]      [description]
     */
    public function __get($var)
    {
        if (isset(get_instance()->$var))
        {
            return get_instance()->$var;
        }
    }

    public function __construct()
    {
        $this->load->library('session');
    }

    public function getKey()
    {
        return $this->auth_key;
    }

    public function setKey($key)
    {
        $this->auth_key = $key;
    }


    /**
     * [callAPService description]
     * @param  [type] $apparams    [description]
     * @param  string $error_redir [description]
     * @return [type]              [description]
     */
    protected function callAPService( $url,  $error_redir = 'nitrocart/cart' )
    {

        // Call AP Service
        $results = $this->getRemoteData( $url, $this->getKey() );

        // Check for errors
        if (isset($results['error']))
        {
            $this->session->set_flashdata( JSONStatus::Error , 'ER3:'. $results['error']['errorMessage'] );

            redirect($error_redir);
        }
        if (isset($results['postage_result']['total_cost']))
        {
            // Return calc
            return $results['postage_result']['total_cost'];
        }

        $this->session->set_flashdata( JSONStatus::Error , 'ER4: Unknown');

        redirect($error_redir);


    }

    private function getRemoteData( $url, $auth_key= 'xxxxxxxxxxxxxxxxxx', $method='json' )
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,$url);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  'Auth-Key: ' . $auth_key
		));

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$contents = curl_exec($ch);

		curl_close($ch);

		//currently only supports json but future we need to support the xml as well
		if($method=='json')
			return json_decode($contents,true);
		else
			return json_decode($contents,true);

	}

    protected function getGirth($height,$width)
	{
		return ($width+$height)*2;
	}
}


/**
 * This allows for multi-value options by same key
 * where the original does not allow this
 */
if ( ! class_exists('APUrlParam')) 
{
    class APUrlParam
    {
        protected $params;
        protected $first;
        protected $test;

        const API_TEST         = 'https://auspost.com.au/api/';
        const API_PUBLIC       = 'https://auspost.com.au/api/';
        const DOMESTIC_PARCEL  = 'postage/parcel/domestic/calculate.json';
        const DOMESTIC_LETTER  = 'postage/letter/domestic/calculate.json';

        public function __construct($from_zip,$to_zip)
        {

            $this->first = true;
            $this->test = false;

            // required
            $this->add('from_postcode', $from_zip );
            $this->add('to_postcode', $to_zip );
        }

        public function setDeliveryMethod($method)
        {
            $this->add('service_code', $method );
        }
        public function addBox($item=array())
        {
            $item['weight'] = ($item['weight']==0)?0.01:$item['weight'];

            $this->add('weight', $item['weight'] );
            $this->add('width', $item['width']);
            $this->add('height', $item['height']);
            $this->add('length', $item['length']);
        }

        public function add($key,$value)
        {
            $this->params .= $this->first ? '?' : '&';
            $this->params .= "{$key}={$value}";
            $this->first = false;
        }

        public function get( $endpoint )
        {
            $api_root       = ($this->test) ? APUrlParam::API_TEST  : APUrlParam::API_PUBLIC  ;
            $api_endpoint   = ($this->test) ? APUrlParam::API_TEST  : APUrlParam::API_PUBLIC  ;

            return $api_root.$endpoint.$this->params;
        }

        public function getDomesticParcelURI()
        {
            $endpoint = self::DOMESTIC_PARCEL;
            $api_root       = ($this->test) ? APUrlParam::API_TEST  : APUrlParam::API_PUBLIC  ;
            $api_endpoint   = ($this->test) ? APUrlParam::API_TEST  : APUrlParam::API_PUBLIC  ;
            return $api_root.$endpoint.$this->params;
        }

        public function clear()
        {
            $this->first = true;
            $this->params = '';
        }

    }
}



/*
 *
 *
 *
 *
 * The below classes /enums are not currently supported
 *
 *
 *
 *
 *
 *
 *
 *
 */


if ( ! class_exists('AustPostServiceCode')) 
{
    class AustPostServiceCode
    {
        //domestic options
        const AUS_LETTER_EXPRESS_SMALL      = 'AUS_LETTER_EXPRESS_SMALL';
        const AUS_LETTER_REGULAR_LARGE      = 'AUS_LETTER_REGULAR_LARGE';
        const AUS_PARCEL_REGULAR            = 'AUS_PARCEL_REGULAR';
        const AUS_PARCEL_EXPRESS            = 'AUS_PARCEL_EXPRESS';
        const AUS_PARCEL_COURIER            = 'AUS_PARCEL_COURIER';
        const AUS_PARCEL_COURIER_SATCHEL_MEDIUM = 'AUS_PARCEL_COURIER_SATCHEL_MEDIUM';
        //international options
        const INTL_SERVICE_AIR_MAIL         = 'INTL_SERVICE_AIR_MAIL';
        const INTL_SERVICE_ECI_D            = 'INTL_SERVICE_ECI_D';
        const INTL_SERVICE_ECI_M            = 'INTL_SERVICE_ECI_M';
        const INTL_SERVICE_ECI_PLATINUM     = 'INTL_SERVICE_ECI_PLATINUM';
        const INTL_SERVICE_EPI              = 'INTL_SERVICE_EPI';
        const INTL_SERVICE_EPI_B4           = 'INTL_SERVICE_EPI_B4';
        const INTL_SERVICE_EPI_C5           = 'INTL_SERVICE_EPI_C5';
        const INTL_SERVICE_PTI              = 'INTL_SERVICE_PTI';
        const INTL_SERVICE_RPI_B4           = 'INTL_SERVICE_RPI_B4';
        const INTL_SERVICE_RPI_DLE          = 'INTL_SERVICE_RPI_DLE';
        const INTL_SERVICE_SEA_MAIL         = 'INTL_SERVICE_SEA_MAIL';
    }
}
if ( ! class_exists('AustPostServiceOption')) 
{
    class AustPostServiceOption
    {
        const AUS_SERVICE_OPTION_COD_MONEY_COLLECTION   = 'AUS_SERVICE_OPTION_COD_MONEY_COLLECTION';
        const AUS_SERVICE_OPTION_COD_POSTAGE_FEES       = 'AUS_SERVICE_OPTION_COD_POSTAGE_FEES';
        const AUS_SERVICE_OPTION_COURIER_EXTRA_COVER_SERVICE = 'AUS_SERVICE_OPTION_COURIER_EXTRA_COVER_SERVICE';
        const AUS_SERVICE_OPTION_DELIVERY_CONFIRMATION  = 'AUS_SERVICE_OPTION_DELIVERY_CONFIRMATION';
        const AUS_SERVICE_OPTION_EXTRA_COVER            = 'AUS_SERVICE_OPTION_EXTRA_COVER';
        const AUS_SERVICE_OPTION_PERSON_TO_PERSON       = 'AUS_SERVICE_OPTION_PERSON_TO_PERSON';
        const AUS_SERVICE_OPTION_SIGNATURE_ON_DELIVERY  = 'AUS_SERVICE_OPTION_SIGNATURE_ON_DELIVERY';
        const AUS_SERVICE_OPTION_STANDARD               = 'AUS_SERVICE_OPTION_STANDARD';
        const AUS_SERVICE_OPTION_REGISTERED_POST        = 'AUS_SERVICE_OPTION_REGISTERED_POST';
        const INTL_SERVICE_OPTION_CONFIRM_DELIVERY      = 'INTL_SERVICE_OPTION_CONFIRM_DELIVERY';
        const INTL_SERVICE_OPTION_EXTRA_COVER           = 'INTL_SERVICE_OPTION_EXTRA_COVER';
        const INTL_SERVICE_OPTION_PICKUP_METRO          = 'INTL_SERVICE_OPTION_PICKUP_METRO';
    }
}
if ( ! class_exists('AustPostDay')) 
{
    class AustPostDay
    {
        const MONDAY        = 1;
        const TUESDAY       = 2;
        const WEDNESDAY     = 3;
        const THURSDAY      = 4;
        const FRIDAY        = 5;
        const SATURDAY      = 6;
        const SUNDAY        = 7;
    }
}
if ( ! class_exists('AustPostState')) 
{
    class AustPostState
    {
        const VIC   = 'VIC';
        const NSW   = 'NSW';
        const TAS   = 'TAS';
        const NT    = 'NT';
        const WA    = 'WA';
        const SA    = 'SA';
        const QLD   = 'QLD';
        const ACT   = 'ACT';
    }
}
if ( ! class_exists('AustPostDeliveryNetwork')) 
{
    class AustPostDeliveryNetwork
    {
        const STANDARD  = '01';
        const EXPRESS   = '02';
    }
}
if ( ! class_exists('AustPostEnvelopeType')) 
{
    class AustPostEnvelopeType
    {
        const INTL_ENVELOPE_TYPE_POSTCARD   = 'INTL_ENVELOPE_TYPE_POSTCARD';
        const INTL_ENVELOPE_TYPE_UP_TO_50G  = 'INTL_ENVELOPE_TYPE_UP_TO_50G';
        const INTL_ENVELOPE_TYPE_50G_250G   = 'INTL_ENVELOPE_TYPE_50G_250G';
        const INTL_ENVELOPE_TYPE_250G_500G  = 'INTL_ENVELOPE_TYPE_250G_500G';
    }
}
