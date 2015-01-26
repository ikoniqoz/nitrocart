<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

class Manual_Gateway {


	public $title = 'Manual - Bank Deposit';
	public $short_title = 'Manual'; //short title is used for transaction line items - much better than storing full nae
	public $description = 'Bank Deposit';
	public $author = 'Sal Bordonaro';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';



	public $fields = array(
			array(
				'field' => 'description',
				'label' => 'Description',
				'rules' => 'trim|max_length[1000]'
			)

		);


	public function __construct() {		}


	public function pre_output()
	{
	
	}

    public function skip_confirmation()
    {
		return false;
    }

	/**
	 * Get the params to send
	 * @param  [type] $billing_address [description]
	 * @param  [type] $payable_items   [description]
	 * @param  [type] $order           [description]
	 * @param  string $curr_code       [description]
	 * @return [type]                  [description]
	 */
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
