<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

class Stripe_Gateway 
{

	public $title = 'Stripe';
	public $short_title = 'Stripe'; //short title is used for transaction line items - much better than storing full nae
	public $description = 'Process Payments via Stripe';
	public $author = 'Sal Bordonaro';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';


	public $fields = array(
		array(
			'field' => 'options[api_key]',
			'label' => 'API Key',
			'rules' => 'trim|max_length[300]|required'
		),
	);


	public function __construct() {		}



    public function pre_output()
    {
    
    }

    /**
     * We need to capture the input
     * @return [type] [description]
     */
    public function skip_confirmation()
    {
        return false;
    }



    /**
     * Returns the pre-process params
     * The input params are required
     * 
     * @param  [type] $billing_address [description]
     * @param  [type] $payable_items   [description]
     * @param  [type] $order           [description]
     * @param  string $curr_code       [description]
     * @return [type]                  [description]
     */
	public function pre_process( $order )
	{
		/*
			$order->billing_address;
			$order->payable_items;
		 	$order->country_currency_code;
		*/
	   $this->params = $this->get_post_get();
       $this->params['api_key'] = $this->options['api_key'];
        return $this->get_post_get();

	}


	public function post_callback($response)
	{
		return NULL;
	}


	public function view_path()
	{
		return  'gateways/'.$this->slug.'/display';
	}

    
    private function get_post_get()
    {
        return isset($_POST) ? $_POST : $_GET ;
    }
}