<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Fastway_core
{

    public function __get($var)
    {
        if (isset(get_instance()->$var))
        {
            return get_instance()->$var;
        }
    }

	public $name = 'Fastway Postage';
	public $description = 'Fast Way Postage';
	public $author = 'inspiredgroup.com.au';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';
	public $tax_rate = 0.1; //10%
	public $__api_key;
	public $__url_ENDPOINTS = array(
			'list_franchises'=>'http://au.api.fastway.org/v3/psc/listrfs/',
			'calc_postage' =>'http://au.api.fastway.org/v3/psc/lookup/',
		);


	public function __construct()
	{

	}

	public $fields = array(
		array(
			'field' => 'apikey',
			'label' => 'API Key',
			'rules' => 'trim|text',
		),
		array(
			'field' => 'RFCode',
			'label' => 'RFCode FastWay franchise code',
			'rules' => 'trim|text',
		),
		array(
			'field' => 'distcode',
			'label' => 'Distribution PostCode',
			'rules' => 'trim|text',
		),		
		array(
			'field' => 'usepackages',
			'label' => 'Use Package or Items',
			'rules' => 'trim|numeric',
		),
		array(
			'field' => 'packagetype',
			'label' => 'Package Type',
			'rules' => 'trim|text',
		),		
		array(
			'field' => 'keepresponses',
			'label' => 'Keep CURL Responses',
			'rules' => 'trim|numeric',
		),	
		array(
			'field' => 'mincharge',
			'label' => 'Min Charge',
			'rules' => 'trim|numeric',
		),	
		array(
			'field' => 'maxcharge',
			'label' => 'Max Charge',
			'rules' => 'trim|numeric',
		),			
		array(
			'field' => 'handling',
			'label' => 'Handling Fee',
			'rules' => 'trim|numeric',
		),		
		array(
			'field' => 'multiregion',
			'label' => 'AllowMultipleRegions',
			'rules' => 'trim',
		),	

	);


	protected function validateAPIKey($options)
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

	protected function validateOptions($options)
	{
		//perhaps try to connect to AustPost for further validation!!
		return true;
	}

	public function setAPI($api)
	{
		$this->__api_key = $api;
	}

	public function getAPI()
	{
		return $this->__api_key;
	}

	public function doCalc( $options, $items, $zip=3000, $suburb = '', $redir_options =[] )
	{

		$dist_zip = (isset($options['distcode']))?$options['distcode']:3000;
		$rfc_code = (isset($options['RFCode']))?$options['RFCode']:'SYD';
		$packagetype = (isset($options['packagetype']))?$options['packagetype']:'Parcel';
		$multi_region  = (isset($options['multiregion']))?$options['multiregion']:'true';

		$api_key = $options['apikey'];

		$this->setAPI( $api_key );


		$_total_cost = 0;
		$__count = 0;


		foreach($items as $item)
		{

			$__count++;

			$app = new FWUrlParam();
	
			$app->set('RFCode', $rfc_code );

			if( trim($suburb) != '')
				$app->set('Suburb', $suburb );


			$app->set('DestPostcode', $zip );
			$app->set('WeightInKg', $item['weight'] );
			$app->set('LengthInCm', $item['length'] );
			$app->set('WidthInCm' , $item['width'] );
			$app->set('HeightInCm', $item['height'] );
			$app->set('AllowMultipleRegions', $multi_region );
			$app->set('ShowBoxProduct', 'false' );
			$app->set('api_key', $api_key );


	        try
	        {
	        	$curl_url = $this->__url_ENDPOINTS['calc_postage'];
		        $_total_cost += ($item['qty'] * $this->callAPService( $app->get($curl_url) , $redir_options['redir'] , $packagetype, $options  ) );
	        }
	        catch (Exception $e)
	        {
	            $msg = "oops: ".$e->getMessage();
	            $this->set_flashdata(JSONStatus::Error, $msg);
	            redirect(NC_ROUTE.'/cart');
	        }

	        //clear
	        $app = NULL;
		}

		//return $__count;
		return $_total_cost;
	}

	public function getFranchiseForOZ($api)
	{
		$app = new FWUrlParam();
		$app->set('CountryCode','1');
		$app->set('api_key', $api );

   		$url = $this->__url_ENDPOINTS['list_franchises'];

		$curl_url = $app->get($url);

   		$results = $this->getRemoteData( $curl_url, 'json', false );

   		$list = [];

        if (isset($results['result'][0]['FranchiseCode']))
        {
	        if (isset($results['result'][0]['FranchiseName']))
	        {
	        	/*
	        		//we are in business
	        		"FranchiseCode":"ADL",
					"FranchiseName":"Adelaide",
					"Phone":"(08) 8345 2300",
					"Fax":"(08) 8345 2388",
					"Add1":"756-758 Port Road",
					"Add2":"",
					"Add3":"",
					"Add4":"",
					"EmailAddress":"julie.baker@fastway.com.au"
				*/
		        // get first in list
				foreach($results['result'] as $rfc_result)
				{
			        $key = $rfc_result['FranchiseCode'];
			        $value =  $rfc_result['FranchiseName'];

			        $list[$key] = $value;
				}


	        	
        	}

        }

        return $list;
	}

	public function getFranchiseIndo($api, $fran_code='SYD')
	{
		$app = new FWUrlParam();
		$app->set('CountryCode','1');
		$app->set('FranchiseeCode',$fran_code);		
		$app->set('api_key', $api );

   		$url = $this->__url_ENDPOINTS['list_franchises'];
		$curl_url = $app->get($url);

   		$results = $this->getRemoteData( $curl_url );

   		$list = [];

        if (isset($results['result'][0]['FranchiseCode']))
        {
	        if (isset($results['result'][0]['FranchiseName']))
	        {
	
			        return $results['result'][0];
        	}

        }

        return NULL;
	}

    /**
     * [callAPService description]
     * @param  [type] $apparams    [description]
     * @param  string $error_redir [description]
     * @return [type]              [description]
     */
    protected function callAPService( $url,  $error_redir = 'nitrocart/cart', $package_type ='Parcel',$options=[] )
    {

        $results = $this->getRemoteData( $url, 'json', (bool) (int) $options['keepresponses'] );

        if (isset($results['result']['services'][0]['totalprice_frequent']))
        {

        	switch($package_type)
        	{
        		case 'Either':     		
        		case 'Parcel':
        			//search for the parcel
        			foreach($results['result']['services'] as $service)
        			{
        				if($service['type'] == 'Parcel')
        				{
        					return $service['totalprice_frequent'];
        				}
        			}     		
        		case 'Satchel':
        			foreach($results['result']['services'] as $service)
        			{
        				if($service['type'] == 'Satchel')
        				{
        					return $service['totalprice_frequent'];
        				}
        			}        		
        		default:   
        			foreach($results['result']['services'] as $service)
        			{
        				return $service['totalprice_frequent'];
        			}  
        			break;
        	}


        }

        // Check for errors
        if (isset($results['error']))
        {
        	//SMR == Shipping Method Response
            $this->session->set_flashdata( JSONStatus::Error , 'ER (SMR):'. $results['error'] );
        }
        else
        {
        	//SYS - System message
 			$this->session->set_flashdata( JSONStatus::Error , 'ER (SYS): Unknown');
        }

        redirect($error_redir);
    }


    private function getRemoteData( $url, $method='json', $store_result = true )
	{
		$ch = curl_init();

		//$url = "http://au.api.fastway.org/v3/psc/lookup/MEL/SYD/200/5?api_key=fbb6f2f76be895dd580081dbab0fe803";
		curl_setopt($ch, CURLOPT_URL,$url);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$contents = curl_exec($ch);

		curl_close($ch);

		if($store_result)
		{
			$time = time();
			$time = substr($time,5);
			$date = new DateTime();
			$timestamp = $date->format("Y-m-d_") . $time;

			//stor the result for testing
			//$timestamp = date("Y-m-d__H.m.s.u");
			$path = SHARED_ADDONPATH . "modules/nitrocart/bak/api_response/response_{$timestamp}_api.json";
			file_put_contents($path, $contents );
		}


		//currently only supports json but future we need to support the xml as well
		if($method=='json')
			return json_decode($contents,true);
		else
			return json_decode($contents,true);
	}

}

/**
 * This allows for multi-value options by same key
 * where the original does not allow this
 */
if ( ! class_exists('FWUrlParam')) 
{
    class FWUrlParam
    {
        protected $params;
        protected $first;


        public function __construct()
        {
            $this->first = true;
        }


        public function set($property, $value)
        {
            $this->_add($property, $value );
        }

       
        protected function _add($key,$value)
        {
            $this->params .= $this->first ? '?' : '&';
            $this->params .= "{$key}={$value}";
            $this->first = false;
        }

        public function get($end_point='')
        {
            return $end_point.$this->params;
        }


        public function clear()
        {
            $this->first = true;
            $this->params = '';
        }

    }
}