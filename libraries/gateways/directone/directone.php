<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Directone_Gateway {


	public $title = 'DirectOne';
	public $short_title = 'DirectOne'; //short title is used for transaction line items - much better than storing full nae
	public $description = 'DirectOne';
	public $author = 'Sal Bordonaro';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';



	//where 1 == test server
	private $servers = array(
			1=>'https://vault.safepay.com.au/cgi-bin/test_payment.pl',
			0=>'https://vault.safepay.com.au/cgi-bin/test_payment.pl'
	);

	public $fields = array(
		array(
			'field' => 'options[test_mode]',
			'label' => 'Test Mode',
			'rules' => 'trim|max_length[100]|numeric'
		),
		array(
			'field' => 'description',
			'label' => 'Description',
			'rules' => 'trim|max_length[1000]|'
		),
		array(
			'field' => 'options[companyname]',
			'label' => 'Company Name',
			'rules' => 'trim|max_length[300]|required'
		),
		array(
			'field' => 'options[vendorname]',
			'label' => 'Vendor Name',
			'rules' => 'trim|max_length[300]|required'
		),
		array(
			'field' => 'options[vendorpin]',
			'label' => 'Vendor PIN',
			'rules' => 'trim|max_length[255]|required'
		),
		array(
			'field' => 'options[vendoradminemail]',
			'label' => 'Admin Email',
			'rules' => 'trim|max_length[255]'
		),
		array(
			'field' => 'options[returnlinktext]',
			'label' => 'Return Text (Link - [255 char max])',
			'rules' => 'trim|max_length[255]'
		),	
	);


	public function __construct() {		}


	/**
	 * Prepare the object for display at the payment page
	 * 
	 * @return [type] [description]
	 */
	public function pre_output()
	{
		//
		// Set a default option
		//
		$this->options['uri'] =  $this->servers[ 1 ];


		// If the correct settings are set, override the defaults
		if(isset($this->options))
		{
			if( isset($this->options['test_mode'] ) )
			{
				$this->options['uri'] =  $this->servers[ $this->options['test_mode'] ];
			}
		}

	}

	/**
	 * Informs the payment controller if this gateway can skip the confirmation page
	 * @return [type] [description]
	 */
    public function skip_confirmation()
    {
		return false;
    }


	public function pre_process( $order )
	{
		$this->params = array();		
		return  array();
	}


	public function post_callback($response)
	{
		return NULL;
	}


	public function view_path()
	{
		return 'gateways/'.$this->slug.'/display';
	}


}
