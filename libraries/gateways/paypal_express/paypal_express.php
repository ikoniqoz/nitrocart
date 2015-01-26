<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

class Paypal_express_Gateway {


	public $title = 'Paypal Express';
	public $short_title = 'PayPal'; //short title is used for transaction line items - much better than storing full nae
	public $description = 'Process Payments via Paypal';
	public $author = 'Sal Bordonaro';
	public $website = 'http://inspiredgroup.com.au';
	public $version = '1.0';
	public $image = '';



	public $fields = array(
		array(
			'field' => 'options[username]',
			'label' => 'Username',
			'rules' => 'trim|max_length[200]|required'
		),
		array(
			'field' => 'options[password]',
			'label' => 'Password',
			'rules' => 'trim|max_length[100]|required'
		),
		array(
			'field' => 'options[signature]',
			'label' => 'API Signature',
			'rules' => 'trim|max_length[200]|required'
		),
		array(
			'field' => 'options[test_mode]',
			'label' => 'Test Mode',
			'rules' => 'trim|max_length[100]|numeric'
		),
		array(
			'field' => 'options[auto]',
			'label' => 'Self submit',
			'rules' => 'trim|max_length[100]|numeric'
		),
	);


	public function __construct() {		}


	/**
	 * Initialize the fields, and get redirect method !
	 * 
	 * @return [type] [description]
	 */
    public function pre_output()
    {
    }

    public function skip_confirmation()
    {
    	if($this->options['auto']==1)
		{
			return true;
		}
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
		$payable_items = $this->_prep_items( $order );
	
        $params = array(
                'email' => $billing_address->email,
                'amount' =>  (float) $order->total_amount_order_wt,
                'currency' => $order->country_currency_code,
                'return_url' =>  base_url() . "shop/payment/callback/".  $order->id,
                'cancel_url' =>  base_url() . "shop/payment/cancel/".  $order->id,
                'phone' => $billing_address->phone,
                'postcode' => $billing_address->zip,
                'country' => $billing_address->country,
                'city' => $billing_address->city,
                'region' => $billing_address->state,
                'address1' => $billing_address->address1,
                'address2' => $billing_address->address2,
                'name' => $billing_address->first_name,
                'first_name' => $billing_address->first_name, //test
                'last_name' => $billing_address->last_name,   //test
                'items' => $payable_items
        );

		$this->params = $params;

		return $params;
	}


	public function post_callback($response)
	{
		return NULL;
	}


	public function view_path()
	{
		return  'gateways/'.$this->slug.'/display';
	}



    /**
     * prepare the order items for use by Paypal
     * 
     * @param  [type] $order [description]
     * @return [type]        [description]
     */
    private function _prep_items( $order )
    {

        //order_m should already be loaded
        $items = $this->orders_m->get_order_items( $order->id );


        $payable_items = array();

        //add the items
        foreach($items as $item)
        {
             $payable_items[] =  array(

                  'name'=> $item->name,
                  'desc'=> '',/*$item->description,*/
                  'amt' => (float) $item->item_subtotal,
                  'qty' => $item->qty,
                );
        }


        if( $order->total_shipping ) //If cost of shipping > 0
        {
            //add the shipping
            $payable_items[] =  array(

                  'name'=>'Shipping and Handling',
                  'desc'=> '',/*$item->description,*/
                  'amt' => (float) $order->total_shipping,
                  'qty' => 1,
            );

        }

        return $payable_items;
    }	

}