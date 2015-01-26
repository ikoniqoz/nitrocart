<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/core/checkout_core.php');

class Checkout extends Checkout_core  
{

    /**
     * @constructor
     */
    public function __construct()
    {
        // call parent constructor for init
        parent::__construct();

        // Set some common breadcrumbs
        $this->template
                ->set_breadcrumb('Home', '/')
                ->set_breadcrumb(Settings::get('shop_name'),'/'.NC_ROUTE)
                ->set_breadcrumb('Cart', '/'.NC_ROUTE.'/cart');
    }

    /**
     * Express places the order directly if they have agreed, they still must purchase: 
     * express will only work if all the active data is correct
     * 
     * display order info (i.e cart info )
     */
    public function express()
    {
        $this->set_step(0);

        $data = new ViewObject();

        // Get the express checkout object
        $ec = $this->getExpressObject();

        //if not exist and false, then redirect
        $ec OR redirect( NC_ROUTE . '/checkout' );
        

        //need these libs for deep pass req.
        //$this->load->model('nitrocart/addresses_m');
        //$this->load->model('nitrocart/orders_m');
        $this->load->library('nitrocart/Gateway_library');

        //if form validation pass
        $this->session->set_userdata('gateway_id',  $ec->gateway->id);
        $this->session->set_userdata('user_id', $this->current_user->id);
        $this->session->set_userdata('billing', $ec->billing_address->id);
        //$this->session->set_userdata('shipping', $ec->shipping_address->id);
        $this->set_shipping_address_id( $ec->shipping_address->id );

        $data->gateway      = $this->gateway_library->get($ec->gateway->id);
        $data->gateway_id   = $ec->gateway->id; //deprecated
       
        $data->billing_address = $ec->billing_address;
        $data->shipping_address = $ec->shipping_address;
        $data->shipments = $ec->shipments;

        $so = $this->set_shipment_method( $ec->shipments->id ); //this also does a calc
        $data->ship_cost = $so->cost;


        if( $input = $this->input->post() )
        {

            $this->load->model('nitrocart/orders_m');

            if($order_id = $this->place_order())
            {
                redirect( NC_ROUTE . '/payment/order/' . $order_id );
            }
            //else
            $this->session->set_flashdata( JSONStatus::Error ,"Unable to place order.");
            redirect( NC_ROUTE. '/cart' );
        }


        $this->template
                ->title(Settings::get('shop_name'), 'Account')        
                ->set_breadcrumb('Express Checkout')
                ->build( 'checkout/express/confirm',$data);
    }


    /**
     * There should be no direct access to index, so this will re-rout to the correct place
     */
    public function index()
    {
        //basically reset the steps
        $this->set_step(0);

        if($this->current_user)
        {
            $this->session->set_userdata('user_id', $this->current_user->id);
            $this->set_step(1);
            redirect( NC_ROUTE .'/checkout/address');
        }
        //else
        redirect( NC_ROUTE . '/checkout/customer');
    }

    /**
     * The initial step if not logged in: Guest must login or if guest are alllowed then they can continue to  billing.
     */
    public function customer()
    {
        
        // Sustomer is stil step 0
        $this->set_step(0);



        // You are logged in, you dont have access here
        if($this->current_user)
        {
            $this->set_step(1);
            redirect( NC_ROUTE. '/checkout/address');
        }



        // Check to see if a field is available
        if( $this->input->post('customer') )
        {

            $input = $this->input->post();
            switch($input['customer'])
            {
                case 'register':
                    $this->session->set_userdata('nitrocart_redirect_to', NC_ROUTE .'/checkout/address');
                    redirect('users/register');
                    break;
                case 'guest':
                    $this->session->set_userdata('user_id', 0);
                    $this->set_step(1);
                    redirect( NC_ROUTE. '/checkout/address');
                    break;
                default:
                    break;
            }
            //more like an system error!
            $this->session->set_flashdata( JSONStatus::Error , 'Invalid selection');
            redirect( NC_ROUTE . '/checkout');
        }

        $this->set_step(0);

        $this->template
                ->title(Settings::get('shop_name'), 'Account')        
                ->set_breadcrumb('User Account')
                ->build( $this->theme_layout_path . 'customer');
    }

    /**
     *
     *
     */
    public function address()
    {

        $this->is_step(1) OR redirect( NC_ROUTE.'/checkout/'); 

        //load up some libs
        $this->load->helper('nitrocart/nitrocart_zone');
        $this->load->model('nitrocart/addresses_m');

        //basic validation
        if($data = $this->_address())
        {
            //Get defaults
            $data = $this->get_user_default_address($data);
            $data->existing_addresses = $this->get_existing_addresses('billing');


            //now check if we are posting back
            if($input = $this->input->post())
            {
                $this->_address_post($input,$data);
            }

            // Finally if we make it here with or without
            // post we pass the data object to display
            $this->_address_display($data);
            
        }
        else
        {
            redirect( NC_ROUTE.'/checkout/');     
        }
    }


    /**
     * Pre-validation of this method
     */
    private function _address()
    {
        $data = new ViewObject();
        $data->has_shippable_items = true;

        //this is a good time to calc shippable items as the nbext screen will be for a shipping address
        $this->count_shippable_items();

        //dont need shipping, so dont show it
        if($this->total_items_require_shipping == 0)
        {
            $this->set_null_shipping_method(4);
            $data->has_shippable_items = false;
        }

        return $data;
        return ($this->is_step(1))?$data:false;
    }  

    /**
     * $data = Object()
     * $data->existing_addresses    = []
     * $data->city                  = 'some current data'
     * $data->address1              = 'some current data'
     * $data->address2              = 'some current data'
     * $data->has_shippable_items   = true|false
     *  
     *
     */
    private function _address_post($input,$data)
    {

        //
        // Remove if has been set
        //
        if(isset($input['submit']))
        {
            unset($input['submit']);
        }

        //
        // First lets see if
        // 
        if(!isset($input['selection']))
        {
            $input['selection'] = '';
        }


        //
        // Determin ehere to go
        //
        switch($input['selection'])
        {
            case 'existing':
                $this->_address_post_existing($input,$data);
                return;
                break;
            case 'new':
                $this->_address_post_new($input,$data);
                return;
            default:
                //hoo ha, how did we get here. ??
                //go home and send mummy a message rto say something is wrong!
                $this->session->set_flashdata('error','Oops, something went wrong!');
                redirect('');                
                break;
        }

        //no way to end up here now. oohh-hahahahah..grunt.
    }

    /**
     * At this point any existing address wil ldefault to SAME_AS_SHIPPING!
     */
    private function _address_post_existing($input,$data)
    {

        $this->form_validation->set_rules('useragreement', 'User Agreement', 'required|numeric|trim');

        //var_dump($input);die;
        //OK great lets check for a billing_address_id
        if(isset($input['billing_address_id']))
        {
            $billing_address_id = (int) $input['billing_address_id'];

            $this->session->set_userdata('billing', $billing_address_id);

            if(isset($input['same_as_billing']))
            {
                $shipping_address_id = $billing_address_id;
            }
            else
            {
                $shipping_address_id = $billing_address_id;
                //$shipping_address_id = //validate and get id;
            }

            //check user agreement
            if (!$this->form_validation->run())
            {
                return;
            }

            //set in session value and continue
            if($this->validate_country_list_by_address_id( $shipping_address_id ))
            {
                //update this row to allow for shipping
                $this->addresses_m->doShippingAlso( $shipping_address_id );
                //$this->session->set_userdata('shipping', $this->input->post('address_id'));
                $this->set_shipping_address_id( $shipping_address_id );
  
                $this->set_step(3);
                redirect( NC_ROUTE. '/checkout/gatreview');
            }
            else
            {
                $this->session->set_flashdata(JSONStatus::Error,'This product can not be shipped to your location.');
                redirect( NC_ROUTE.'/checkout/address');
            }
        }
        else
        {
            $this->session->set_flashdata('error','Billing Address not set.');
            redirect(NC_ROUTE . '/checkout/address');
        }     
    }

    private function _address_post_new($input,$data)
    {

        $shipping_address = [];


        $same_as_billing = (isset($input['same_as_billing']))?true:false;


        if($same_as_billing)
        {
            //remove all shipping_ fields
            foreach($input as $key=>$value)
            {
                if(substr($key,0,9)=='shipping_') unset($input[$key]);
            }   

            //$shipping_address['instruction'] = isset($input['instruction'])?$input['instruction']:'';     
        }
        else
        {
            //lets get a seperate array of data for the input
            foreach($input as $key=>$value)
            {
                if(substr($key,0,9)=='shipping_') $shipping_address[$key] = $value;
            }              
            $this->form_validation->set_rules( $this->addresses_m->shipping_address_validation );
        }

        $this->form_validation->set_rules( $this->addresses_m->address_validation );
        //$this->form_validation->set_rules('state', 'State', 'callback_country_state_check'); 
        $this->form_validation->set_rules('useragreement', 'User Agreement', 'required|numeric|trim');  


     

        //load up some libs
        $this->load->helper('nitrocart/nitrocart_zone');
        $this->load->model('nitrocart/addresses_m');



        if ($this->form_validation->run())
        {
            $input = $this->input->post();
            $input['user_id'] = $this->session->userdata('user_id');

            //add the new address, note this is for billing
            $address_id = $this->addresses_m->create($input);

            $this->session->set_userdata('billing', $address_id);

            if ($same_as_billing)
            {
                //$this->validate_country_by_country_code( $country_code = 'AU' )
                if($this->validate_country_list_by_address_id( $address_id ))
                {   
                    $this->set_shipping_address_id( $address_id );
                }
                else
                {
                    $this->session->set_flashdata(JSONStatus::Error,'This product can not be shipped to your location.');
                    redirect( NC_ROUTE.'/checkout/address');                        
                }
            }
            else
            {
                $shipping_address_id = $this->addresses_m->create($shipping_address);

                if($this->validate_country_list_by_address_id( $shipping_address_id ))
                {   
                    $this->set_shipping_address_id( $shipping_address_id );
                }
                else
                {
                    $this->session->set_flashdata(JSONStatus::Error,'This product can not be shipped to your location.');
                    redirect( NC_ROUTE.'/checkout/address');                        
                }
            }

            $this->set_step(3);
            redirect( NC_ROUTE.'/checkout/gatreview');
        }
        else
        {
            //validation failed.              
        }     
    }


    /**
     * Displays the User address input form for the checkout
     */
    private function _address_display($data)
    {
        $this->form_validation->set_rules('useragreement', 'User Agreement', 'required|numeric|trim');

        $this->template
                ->title(Settings::get('shop_name'),'Addresses')        
                ->set_breadcrumb('User Account',NC_ROUTE.'/checkout')
                ->set_breadcrumb('Billing')
                ->build($this->theme_layout_path .'address', $data);    
    }



    /**
     * Step 5
     */
    public function gatreview()
    {

        //downstep
        $this->is_step(3) OR redirect( NC_ROUTE.'/checkout/address'); 
        $this->load->model('nitrocart/addresses_m');      
        $this->load->model('nitrocart/orders_m');  
        $this->load->library('nitrocart/Gateway_library');
        $this->load->library('nitrocart/shipping2_library');


        //
        //
        // Shipment
        //
        //


        $data = new ViewObject();

        //get all
        $data->shipments = $this->shipping2_library->get_all_installed(true);

        //calc
        $this->calc_all_shipping($data->shipments , $this->session->userdata('shipping') );


        //shipping
        switch(count($data->shipments))
        {
            case 0:
                if( NC_REQSHIPPING )
                {
                    $this->session->set_flashdata( JSONStatus::Error, 'If you are the site owner, please configure your shipping options.');
                    redirect( NC_ROUTE.'/cart');  
                }
                else
                {
                    //cool, we dont need to condition the check as below;
                }
            default:
                //continue as planned
                $this->form_validation->set_rules('shipment_id', lang('nitrocart:checkout:shipment'), 'required|numeric|trim');
                
                break;
        }


        $data->cart = nc_cart_contents();
        $data->shipping_cost = (float) nc_format_price($this->session->userdata('shipping_cost'));
        $data->shipping_address = (array) $this->addresses_m->get( $this->session->userdata('shipping') );
        $data->order_total_clean = $this->mycart->total() + $data->shipping_cost;
        $data->order_total = nc_format_price( $data->order_total_clean );



        $input = $this->input->post();

        

        //set rules
        $this->form_validation->set_rules('gateway_id', 'gateway', 'required|numeric|trim');
        
        //validate if postback
        if ($this->form_validation->run())
        {
            $this->set_step(6); 
           
            $this->session->set_userdata('gateway_id', $this->input->post('gateway_id'));
            $this->set_shipment_method($this->input->post('shipment_id') );            

            //now place order
            $order_id = $this->place_order();

            if( $order_id > 0)
            {
                redirect( NC_ROUTE.'/payment/order/'.$order_id);
            }

        }

        //we have multiple so the end user can select
        $data->gateways = $this->gateway_library->get_enabled();
        $this->set_step(3);
        $this->_displayMultipleGateways($data);                
           

        return false;
    }


    private function _displayMultipleGateways($data)
    {
        $this->set_step(3);
                
        $this->template
                ->title( Settings::get('shop_name'), '')
                ->set_breadcrumb('User Account',NC_ROUTE.'/checkout')
                ->set_breadcrumb('Billing',NC_ROUTE.'/checkout/billing')
                ->set_breadcrumb('Shipping',NC_ROUTE.'/checkout/shipping');
                if( $this->show_review_order_page ) 
                {
                    $this->template->set_breadcrumb('Review Order',NC_ROUTE.'/checkout/gatreview');
                }
                $this->template
                        ->set_breadcrumb('Payment Options')
                        ->build($this->theme_layout_path . 'gatreview', $data);
    }


}