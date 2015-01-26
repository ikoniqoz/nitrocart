<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Payment extends Public_Controller
{


    const KEEP_LOG = true;


    /**
     * Allow unsafe callbacks for deprecated gateways. It is 
     * highly recomended this is set to false.
     * However if you are using the old payment gateways and 
     * taking your time to move over then you will need this 
     * to true
     *
     * @var boolean
     */
    protected $allow_unsafe_callbacks = true;
    




    /**
     * This is a required value as of 2.0
     * Version specific access in some case
     * @var float
     */
    protected $version =  2.0;




    /**
     * If set to true then system will use multi-thread on curl
     * Note this is experimetal and should be set to NULL on 
     * pro boxes until further testing
     * @var [type]
     */
    protected $allow_multi_thread = NULL;





    /**
     * Leave this as a public field so that HYPER-V can access it
     * @var boolean
     */
    public $hyper_v_protection = true;






    /**
     * Load all the libs, check any security concerns
     */
    public function __construct()
    {
        parent::__construct();
        Events::trigger('SHOPEVT_ShopPublicController');

        Settings::get('shop_open_status') OR redirect( NC_ROUTE.'/closed');

        $this->load->model('nitrocart/orders_m');
        $this->load->library('nitrocart/gateway_library');
        $this->lang->load('nitrocart/merchant');  

        $this->template
                ->set_breadcrumb('Home', '/')
                ->set_breadcrumb(Settings::get('shop_name'),'/'.NC_ROUTE)
                ->set_breadcrumb('Payment');
    }


    /**
     * This displays the facility to pay for an order based on the selected gateway set in the order params
     */
    public function order( $order_id = 0 )
    {

        /**
         * Get the order and accompanying  details 
         * if there is an error a redirect will occur and not continue
         * @var [type]
         */
        $data = $this->_order( $order_id );


        /**
         * This is essentially a Pre-Output of fields, it initializes any fields
         * for display to the user.
         * If the gateway has any custom fields they can be initialized here
         */
        $data->gateway->pre_output();




        /**
         * prep any deprecated fields for display
         * $data->gdata is deprecated
         */
        $this->prep_deprecated( $data );



        // Display the gateway page with their own option (if they have any)
        $this->template
            ->enable_parser(true)
            ->set_breadcrumb('Order')
            ->title( Settings::get('shop_name') )
            ->build( $data->view_file , $data );
    }


    /**
     * preload order info
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    private function _order( $order_id )
    {

        $this->load->model('nitrocart/addresses_m');



        $data = $this->_get_payment_vars();



        /**
         * Assign an order to the view object
         * @var [type]
         */
        $data->order = $this->orders_m->get($order_id);


        if(!$data->order)
        {
            echo "Not a valid order id.";die;
        }


        /**
         * Get the order items, some gateways will use this info
         * @var [type]
         */
        $data->order_items = $this->orders_m->get_order_items($order_id);



        /**
         * Get billing and Shipping Address
         * @var [type]
         */
        $data->billing = $this->addresses_m->get($data->order->billing_address_id);
        $data->shipping = $this->addresses_m->get($data->order->shipping_address_id);


        /**
         * Get the gateway the user had selected via checkout
         * @var [type]
         */
        $data->gateway = $this->gateway_library->get( $data->order->gateway_id );



        return $data;
    }


    /**
     * Retrieve the payment vars from the post
     * @return ViewObject [description]
     */
    private function _get_payment_vars()
    {

        /**
         * Create the default ViewObject
         * @var [type]
         */
        $data = new ViewObject(); 


        // Variables
        $data->months = [];
        $data->years = [];
        $data->start_years = [];
        $data->default_cards = [];


        $currentMonth  = (int) date('m');

        for ($x = $currentMonth; $x < $currentMonth+12; $x++) 
        {
            $time = mktime(0, 0, 0, $x, 1);
            $data->months[date('m', $time)] = date('F', $time);
        }

        $current_year = date('Y');

        for ($i = $current_year; $i < $current_year + 15; $i++)
            $data->years[$i] = $i;


        $current_year = date('Y');

        for ($i = $current_year; $i > $current_year - 15; $i--)
            $data->start_years[$i] = $i;


        
        $data->default_cards['visa']        = 'Visa';
        $data->default_cards['maestro']     = 'Maestro';
        $data->default_cards['mastercard']  = 'MasterCard';
        $data->default_cards['discover']    = 'Discover';

        return $data;
    }


    /**
     * Although this api/endpoint can be called, it will process and redirect
     * There is no view/template to this
     * 
     * @param  integer $order_id [description]
     * @return [type]            [description]
     */
    public function process($order_id = 0)
    {



        /**
         * Load session lib and other required files
         */
              
        $this->load->library('nitrocart/merchant');
        $this->load->model('nitrocart/addresses_m');
        $this->load->model('nitrocart/transactions_m');       
        $this->load->library('nitrocart/currency_library');

        /**
         * Validate and only way to return is if order is valid
         * Otherwise it should redirect
         * @var [type]
         */
        $data = $this->_process($order_id);
        if( ! $data )
        {   
            return false;
        }




        /**
         * Initialize CI-Merchant
         */
        $this->merchant->load( $data->gateway->slug );






        /**
         * Initialize the Merchant Object with Settings
         */
        $this->merchant->initialize( $data->gateway->options );





        /**
         * Call the merhant with data, handle the redirct if need
         */
        $response  = $this->merchant->purchase( $data->gateway->params );



        /**
         * Handle the response
         */
        switch( $response->status() )
        {
            case Merchant_response::AUTHORIZED:            
            case Merchant_response::COMPLETE:
                break;
            case Merchant_response::REDIRECT:
            case Merchant_response::REFUNDED:
            case Merchant_response::FAILED:
                $this->session->set_flashdata( JSONStatus::Error, $response->message());
                redirect( NC_ROUTE . '/payment/order/' . $order_id );
            default:
                break;
        }
   

        

        /**
         *
         * Log the transaction
         */
        $this->transactions_m->merchant_response(   $data->order->id, 
                                                    0,/*(float) $response->amount()*/
                                                    $response->reference(),
                                                    0, 
                                                    $data->gateway->id,
                                                    $response->status(),
                                                    $response->data() 
                                                );




        /**
         * Handle the response if done on the page
         * so if its not a redirect it will continue here
         */
        $this->_merchant_response( $data, $response );



        /**
         * We should not really reach this point of the execution.
         */
        $this->session->set_flashdata( JSONStatus::Notice , "Order Pending." );




        /**
         * Return back to a common route
         */
        redirect( NC_ROUTE. '/');
    }



    /**
     * [_process description]
     * @param  integer $order_id [description]
     * @return [type]            [description]
     */
    private function _process( $order_id = 0)
    {

        /**
         * Get the data object
         * 
         * @var [type]
         */
        $data = $this->_build_data( $order_id );




        /**
         * Get Billing Address for Merchant
         */
        $data->order->billing_address   = $this->addresses_m->get( $data->order->billing_address_id );






        /**
         * We need the currency library to send to the gatewaus for calc.
         * So the gateways know which currency we are dealing with.
         */
        $data->order->country_currency_code = $this->currency_library->getCountryISOCode();




        /**
         * 
         */
        $data->gateway->pre_process( $data->order );





        /**
         * Store the params in the data field ( nct_orders.data )
         */
        $this->orders_m->store_order_params( $data->order->id, $data->gateway->params );




        /**
         * Return the object
         */
        return $data;
    }


    
    /**
     * base_url()/shop/payment/callback/{{order-id}}
     * Handled by merchant-redirect response, this is also considered an unsafe callback
     * @param  [type]   $order_id [description]
     * @return function           [description]
     */
    public function callback($order_id)
    {


        /**
         * Load session lib and other required files
         */
        $this->load->library('session');
        $this->lang->load('nitrocart/merchant');        
        $this->load->library('nitrocart/merchant');



        /**
         * pre-val checks
         */
        $this->_callback( $order_id );




        /**
         * [$data description]
         * @var [type]
         */
        $data    = $this->_build_data( $order_id );





        /**
         * 
         */
        $this->merchant->load( $data->gateway->slug );




        /**
         * init
         */
        $this->merchant->initialize( $data->gateway->options );



        /**
         * Get the params for this order
         * @var [type]
         */
        //$params = (array) json_decode($data->order->data);
        $params = json_decode($data->order->data);



        /**
         * [$response description]
         * @var [type]
         */
        $response = $this->merchant->purchase_return( $params );




        /**
         * Lets see if our gateway has any specif code to run
         */
        $gateway->post_callback($response);




        /**
         * Records the transactions
         */
        $this->_merchant_response( $data, $response );



        /**
         * A redirect will occur within _merchant_response
         */
        return false;
    }



    /**
     * [_callback description]
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    private function _callback( $order_id )
    {
        return true;
    }




    /**
     * Result of this is either redirected r data->order + data->gateway
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    private function _build_data( $order_id )
    {

        /**
         * Load session lib and other required files
         */
        $this->load->library('session');


        /**
         * [$data description]
         * @var ViewObject
         */
        $data = new ViewObject();



        /**
         * Get the order from the Database, we  really need the params
         * @var [type]
         */
        $data->order    = $this->orders_m->get($order_id);





        /**
         * No order exist
         */
        if( ! $data->order )
        {
            $this->session->set_flashdata( JSONStatus::Error , lang('nitrocart:payments:order_does_not_exist') );
            redirect( NC_ROUTE );
        }





        /**
         * Admin deleted order
         */
        if( $data->order->deleted != NULL )
        {
            $this->session->set_flashdata( JSONStatus::Error ,'Order is deleted' );
            redirect( NC_ROUTE );             
        }




        /**
         * Order has been marked as paid already
         */
        if( $data->order->paid_date != NULL )
        {
            $this->session->set_flashdata( JSONStatus::Error , lang('nitrocart:payments:order_has_been_paid') );
            redirect( NC_ROUTE.'/my/orders/order/'.$data->order->id );                
        }




        /**
         * Get the gateway from the order
         */
        $data->gateway = $this->gateway_library->get( $data->order->gateway_id );



        /**
         * Gateway not found
         */
        if( ! $data->gateway )
        {
            $this->set_flashdata( JSONStatus::Error ,'Unable to process order. Cant find valid gateway');
            redirect( NC_ROUTE.'/' );
        }

        return $data;
    }




    /**
     * Handles cancel from payment gateway
     *
     */
    public function cancel($order_id)
    {

        $this->load->model('nitrocart/transactions_m');

        $data = isset($_POST) ? $_POST : $_GET ;

        $this->transactions_m->gateway_cancel( $order_id, $data  );

        if ( $this->_is_https() )
        {
            $this->_remove_https();
        }
   
        // Alt, in future we may want an entice page
        $this->session->set_flashdata('notice', lang('nitrocart:payments:payment_cancelled'));
        redirect( NC_ROUTE );       
    }




    /**
     * [_merchant_response description]
     * @param  [type]  $order         [description]
     * @param  [type]  $gateway_title [description]
     * @param  [type]  $response      [description]
     * @param  integer $redir_option  [description]
     * @return [type]                 [description]
     */
    private function _merchant_response( $data , $response, $redir_option = 1)
    {


        $__ORDER_ID =  $data->order->id;
        $__ORDER_TOTAL = $data->order->total_amount_order_wt;
        $__TXN_REASON = 'PAYMENT';
        $__REFUND = 0;
        $__GATEWAY_TITLE = $data->order->title;
        $__STATUS = $response->status();
        $__DATA_FIELD = $this->get_post_get();

        /*
        $response->reference();
        $response->data();
        $response->message();
        $response->status();
        */

        /**
         * Decide how and where we redirect after...
         * @var [type]
         */
        $redir = ( ($redir_option==1) AND ($this->current_user))?  NC_ROUTE. '/my/orders/order/'. $data->order->id : NC_ROUTE.'/' ;


        switch($response->status())
        {
            case Merchant_response::AUTHORIZED:            
            case Merchant_response::COMPLETE:
                $this->orders_m->mark_as_paid( $order->id );
                Events::trigger('evt_order_paid',  $order->id );
                break;
            case Merchant_response::REDIRECT:
            case Merchant_response::REFUNDED:
            case Merchant_response::FAILED:
                $__TXN_REASON = 'PAYMENT_FAIL';
            default:
                break;
        }

        /**
         * Log the transaction
         */
        $this->transactions_m->merchant_response( $__ORDER_ID , $__ORDER_TOTAL, $__TXN_REASON, $__REFUND, $__GATEWAY_TITLE, $__STATUS , $__DATA_FIELD );

        redirect( $redir );
    }




    /**
     * 
     * @return boolean true|false if HTTPS
     */
    private function _is_https()
    {
        if (strtolower(substr(current_url(), 4, 1)) == 's')
        {
            return true;
        } 

        return false;       
    }



    /**
     * Remove the https fromurl if set.
     * This is genrally called after secure transactions
     * @return [type] [description]
     */
    private function _remove_https()
    {
        $site_url = strtolower(current_url());
        if ( substr($site_url, 4, 1) == 's' )
        {
            redirect(str_replace('https:', 'http:', $site_url ) . '?session=' . session_id());
        }

        // how did we get here ?
        redirect( site_url(NC_ROUTE) );        
    }




    /**
     * @deprecated - Remove from 2.2.7
     *
     * 
     * This function is dangerous to use and should use callback instead.
     * This will be removed in dur course!
     *
     * @param  [type] $order_id [description]
     * @return [type]           [description]
     */
    public function rc($order_id)
    {
        $this->load->library('session');

        if( ! $this->allow_unsafe_callbacks )
        {
            $this->session->set_flashdata( JSONStatus::Error, 'Cant allow this operation');
            redirect( NC_ROUTE );
        }

        $this->orders_m->mark_as_paid( $order_id );
        Events::trigger('evt_order_paid',  $order_id );
        redirect( NC_ROUTE. '/'); 
    }


    /**
     * @deprecated as ofg 2.2.7
     * @param  &      $data [description]
     * @return [type]       [description]
     */
    private function prep_deprecated( & $data)
    {
        $data->gdata = $data->gateway->options;

        $data->view_file = $data->gateway->view_path();
    }


    /**
     * Get the request variables/values
     * @return [type] [description]
     */
    private function get_post_get()
    {
        return isset($_POST) ? $_POST : $_GET ;
    }

}