<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Checkout_core extends Public_Controller {

    #region fields

    public $checkout_options;
    protected $checkout_name = 'multistep';
    protected $checkout_version = '1.6';
    protected $show_review_order_page = true;
    protected $is_guest = true;

    protected $previous_step = 0;    
    protected $current_step = 0;   
    protected $is_dev_mode = false;


    protected $total_items_require_shipping;
    #endregion

    /**
     * Checkout_core constructor initializes class and determines if user can proceed
     */
    public function __construct()
    {

        parent::__construct();


        // call event for extention module integration
        Events::trigger('SHOPEVT_ShopPublicController');


        $this->previous_step = (($this->session->userdata('previous_step'))? $this->session->userdata('previous_step') : 0 ) ;
        $this->current_step =  (($this->session->userdata('current_step'))? $this->session->userdata('current_step') : 0 );
        $this->total_items_require_shipping = (($this->session->userdata('total_items_require_shipping'))? $this->session->userdata('total_items_require_shipping') : 0 );


        //are we working on a dev site
        if (ENVIRONMENT == PYRO_DEVELOPMENT)
        {
            $this->is_dev_mode = true;
        }


        // Set the checkout theme
        $this->theme_layout_path =  'checkout/'.$this->checkout_name.'/';


        //determin if the user is a guest or logged in memeber
        $this->is_guest =  ($this->current_user) ? false : true  ;   //is the user a guest - make this variable available everywhere on this class/controller



        // Intialize default values for checkout process (security ect) Must be called before InitCheckout
        // Need to converti these to checkoutOptions
        $this->checkout_options = new ViewObject();
        $this->checkout_options->canProceed = true; //by default
        $this->checkout_options->restrictionMessage  = "You do not have access";
        $this->checkout_options->canUseExpressCheckout = false; //by default false until we analyze.


        // InitCheckout is called when the checkout controller initializes. 
        // It is for other modules to initize the options
        Events::trigger('SHOPEVT_InitCheckout',  $this->checkout_options);


        // Redirect if no access
        Settings::get('shop_open_status') OR redirect(NC_ROUTE.'/closed');


        // Determn if we should proceed based on cart item count
        if($this->mycart->total_items() <= 0)
        {
            $this->session->set_flashdata( JSONStatus::Error ,'You have no items in the cart to checkout');
            redirect(NC_ROUTE.'/cart');
        }


        // Determin if we can proceed based on extention modules
        // Some extention module restrict users proceeding based on custom param
        if (!$this->checkout_options->canProceed) 
        {
            $this->session->set_flashdata( JSONStatus::Error ,'CO-OPT:'.$this->checkout_options->restrictionMessage);
            redirect(NC_ROUTE.'/cart');
        }


        // Can a guest checkout
        if($this->is_guest)
        {
            if(Settings::get('shop_allow_guest_checkout')==false)
            {
                //we obviously allow 1 endpoint for guest to access
                if($this->uri->segment(3) != 'customer')
                {
                    $this->session->set_flashdata( JSONStatus::Error , "You must be logged in to purchase from the SHOP." );
                    redirect(NC_ROUTE.'/checkout/customer');
                }
            }
        }


        // Determin if we should proceed based on the store type that we have
        $this->shop_type = Settings::get('shop_store_type');              
        if($this->shop_type =='showcase')
        {
            $this->session->set_flashdata( JSONStatus::Error ,'There is no checkout facility on this store.');
            redirect(NC_ROUTE);
        }




        $this->load->library('nitrocart/Gateway_library');
        $this->gateway_count = $this->gateway_library->count_enabled();
        if( $this->gateway_count < 1 )
        {
            $this->session->set_flashdata(JSONStatus::Error, 'Invalid Payment Gateway, unable to proceed until the Payment Gateways have been setup.');
            redirect( NC_ROUTE.'/cart');
        }


        // Determin if we should switch to htps (Do we need to change to SSL)
        if ($this->settings->shop_ssl_required and strtolower(substr(current_url(), 4, 1)) != 's')
        {
            redirect(str_replace('http:', 'https:', current_url()) . '?session=' . session_id()); //exit();
        }

                   
        // Get the Session ID
        if ($this->input->get('session'))
        {
            session_id($this->input->get('session'));
            session_regenerate_id();
        }

        // Set the updated session id
        $this->_session = session_id();        
    }


    /**
     * Counts the shippable items in the cart
     * @return [type] [description]
     */
    protected function count_shippable_items()
    {
        $count = 0;
        foreach($this->mycart->contents() as $item)
        {
            //var_dump($item);die;
            if((int)$item['is_shippable']==1) $count++;
        }

        $this->total_items_require_shipping = $count;
        $this->session->set_userdata('total_items_require_shipping', $this->total_items_require_shipping ); 
    }


    protected function set_step($cur)
    {
        $this->previous_step    =  $this->session->set_userdata('previous_step',$this->current_step);
        $this->current_step     =  $this->session->set_userdata('current_step',$cur);
    }
    protected function is_step($step)
    {
        //var_dump($this->current_step);die;
        if($this->current_step >= $step)
        {
            return true;
        }
        return false;
    }

    /**
     * Place order will place the order and return a status
     */
    protected function place_order()
    {
        //quick hack
        $this->load->model('nitrocart/orders_m');
        $this->load->model('nitrocart/customers_m');


        // Collect data from session/cart and user
        $input = $this->_get_order_params();



        // We want to track|identify customers
        // so if the user is not a guest / or we have a current user!! record the customer 
        $order_id = $this->orders_m->create( $input );
        
        //if($input['user_id']!=0)
        if(!$this->is_guest)
        {
            // 
            // We want to either create a customer record, or update the customer recod
            //
            $this->customers_m->record($input['user_id'], $this->current_user->first_name ,$order_id);
        }

        if ($order_id)
        {

            $this->load->model('nitrocart/transactions_m');


            // Packing slip details
            $this->transactions_m->log($order_id, 0,  0 ,'SYSTEM', 'Packing Slip','accepted', $this->session->userdata('packing_slip') );



            // Store the new order id in session
            $this->session->set_userdata('order_id', $order_id);


            $this->session->set_flashdata(JSONStatus::Success, lang('nitrocart:checkout:order_has_been_placed'));


            //prepare data for event
            $input['order_id'] = $order_id;


            // Notify Users/admin with Emails, and notifies any other module that needs it
            Events::trigger('SHOPEVT_OrderPlaced',  $input, $this->is_guest );


            // Now write a transaction record
            $tran_id = $this->transactions_m->log_new_order($order_id);




            //destroy cart after the order is placed and notifications sent
            $this->mycart->destroy();

            //remove items in the cart
            if($this->current_user)
            {
                $this->load->model('nitrocart/carts_m');            
                $this->carts_m->destroy($this->current_user->id);
            }


            // Step 6
            return $order_id;
        }


        // You need to select a payment method
        $this->session->set_flashdata(JSONStatus::Error, 'Unable to place order - ERROR (Checkout:Line # ' . __LINE__ .' )');

        return false;
    }




    /**
     * Test and validate if user can do express checkout
     */
    protected function getExpressObject()
    {
        $ret_object = new ViewObject();
        $ret_object->validated = true;

        if($this->current_user)
        {
            $ret_object->billing_address = $this->db->where('billing',1)->where('deleted',NULL)->where('user_id', $this->current_user->id)->get('nct_addresses')->row();
            $ret_object->shipping_address = $this->db->where('shipping',1)->where('deleted',NULL)->where('user_id', $this->current_user->id)->get('nct_addresses')->row();

            $ret_object->gateway = $this->db->where('module_type','gateway')->where('enabled',1)->get('nct_checkout_options')->row();
            $ret_object->shipments = $this->db->where('module_type','shipping')->where('enabled',1)->get('nct_checkout_options')->row();

            if ( 
                    ($this->mycart->total_items() > 0) AND 
                    $ret_object->billing_address AND 
                    $ret_object->shipping_address AND 
                    $ret_object->gateway AND 
                    $ret_object->shipments  )
            {
                return $ret_object; 
            }
        }

        return false;
    }


    protected function _get_order_params()
    {
        $input['user_id'] =  $this->session->userdata('user_id');
        $input['cost_items'] =  $this->mycart->total();
        $input['cost_shipping'] =   $this->session->userdata('shipping_cost');
        $input['shipping_id'] =  $this->session->userdata('shipment_id');
        $input['gateway_method_id'] =  $this->session->userdata('gateway_id');
        $input['billing_address_id'] = $this->session->userdata('billing');
        $input['shipping_address_id'] = $this->session->userdata('shipping');
        $input['session_id'] = $this->_session;
        $input['ip_address'] =  $this->input->ip_address();
        $input['checkout_version'] = $this->checkout_version;
        $input['order_total'] = ($input['cost_items'] + $input['cost_shipping'] );
        $input['cart_items'] = $this->mycart->contents();
        $input['has_shipping_address'] = $this->session->userdata('has_shipping_address');
        $input['shipping_tax'] = $this->session->userdata('shipping_tax');
        return $input;
    }




   /**
    * This is used for display for multiple shipping options
    */
    protected function calc_all_shipping($shipping_methods ,$address)
    {
        $ret_array = [];
        $address = $this->addresses_m->get($address);

        foreach ($shipping_methods as $shipping_method)
            $shipping_method->shipping_cost = $this->calc_shipping_by_id($shipping_method->id, $address);

        return $ret_array;
    }


   /**
    * @param INT $id The shipping ID to calc by
    * @param String $address The address to deliver to
    * @param Array $parcels The array of parcels to deliver
    */
    protected function calc_shipping_by_id( $id, $to_address )
    {
        $this->load->library('nitrocart/shipping2_library');
        //dispatch address
        $from_address = [];

        $cart_items = $this->mycart->contents();

        // Create a dispatcher/shipping method object
        $dispatcher = $this->shipping2_library->get_installed( $id );
        $shipping_object = $dispatcher->calc($dispatcher->options, $cart_items , $to_address);
        $shipping_object->id = $id;
  
        Events::trigger('SHOPEVT_CalcShipping', $shipping_object );

        return $shipping_object;
    }


    /*
     * Validation and support functions for checkout 
     */
    protected function validate_country_list_by_address_id( $__shipping_id=-100 )
    {

        $__selected_add = $this->db->where('id', $__shipping_id)->where('user_id', $this->current_user->id)->get('nct_addresses')->row();

        if($__selected_add)
        {
            $country = nc_country($__selected_add->country);

            //validate the users country location for shipping
            return $this->validate_country_list($country->id);
        }

        return false;
    }

    protected function validate_country_by_country_code( $country_code = 'AU' )
    {

        $country = nc_country($__selected_add->country);

        //validate the users country location for shipping
        return $this->validate_country_list($country_id);
    
    }
    /**
     * Validate sthe users shipping country
     * with all list to see if the products can be shipped.
     */
    protected function validate_country_list($selected_county_id = -2740)
    {
        $this->load->helper('nitrocart/nitrocart_zone');

        $master_country_list = $this->db->where('enabled',1)->get('nct_countries')->result();

        $cart_items = $this->mycart->contents();
        $pass = false;


        foreach($cart_items as $key => $item)
        {
            $var_id = (int) $item['variance'];
            if($row = $this->db->where('id',$var_id)->get('nct_products_variances')->row())
            {
                //first step validate against MCL, however do not agree right away
                if( validate_product_against_master_list($selected_county_id,$master_country_list) )
                {
                    //if zoned to the MCL then pass
                    if ( ($row->zone_id == 0) || ($row->zone_id == NULL) )
                    {
                        return true; //automatically pass
                    }
                    //otherwise check against the zone specifically
                    else if($row->zone_id > 0)
                    {
                        if(validate_product_against_active_list($selected_county_id, $row->zone_id ))
                        {
                            return true;
                        }
                    }
                    //all else fails
                } 
                else
                {
                    //user selected county is not even in this list
                }                 
            
                //echo 'cant validate against master list';die;
                
            }

            //echo 'Can not find the variance.!';die;
            
        }

        return false;
    }



    protected function get_user_default_address($data)
    {
        $this->load->helper('nitrocart/nitrocart_zone');

        if ($this->current_user)
        {
            $data = $this->current_user;

            //initialize required with blanks
            $data->address1 = $data->address2 = $data->city = $data->zip = $data->state =' ';

            //otherwise use a post back if it exist if not on user record
            foreach ($this->addresses_m->address_validation AS $rule)
            {
                $data->{$rule['field']} = isset($this->current_user->{$rule['field']}) ? trim($this->current_user->{$rule['field']} ): trim($this->input->post($rule['field']) );
            }            
        }

        return $data;
    }

    protected function get_existing_addresses($type='billing')
    {
        $this->load->helper('nitrocart/nitrocart_zone');

        if ($this->current_user)
        {
            $arr = $this->db->where($type,1)->where('deleted',NULL)->where('user_id', $this->current_user->id)->get('nct_addresses')->result();
            return nc_fetch_country_labels($arr);
        }
        return [];
    }

    protected function country_state_check($str)
    {

        if (strpos($str,'---') !== false) 
        {
            $this->form_validation->set_message('country_state_check', 'The State field must be selected');
            return false;
        }
        return true;
    }


    protected function set_null_shipping_method($set_step=4)
    {
        //$this->session->set_userdata('shipping', NULL);
        //$this->session->set_userdata('has_shipping_address', 0);
        $this->set_shipping_address_id(NULL);
        $this->session->set_userdata('shipment_id', 0 );
        $this->session->set_userdata('shipping_cost', 0 );    
        $this->session->set_userdata('shipping_tax', 0 );   
        $this->session->set_userdata('packing_slip', '' );   
        $this->set_step($set_step);
    }
    protected function set_shipping_address_id($id)
    {
        $this->session->set_userdata('shipping', $id);
        $this->session->set_userdata('has_shipping_address', $id); 
    }

    protected function _reset_session_checkout()
    {
        $this->session->unset_userdata('shipping');
        $this->session->unset_userdata('has_shipping_address');
        $this->session->unset_userdata('shipment_id');
        $this->session->unset_userdata('shipping_cost');    
        $this->session->unset_userdata('shipping_tax');   
        $this->session->unset_userdata('shipping');
        $this->session->unset_userdata('has_shipping_address'); 
        $this->session->unset_userdata('packing_slip');   
        $this->set_step(0);        
    }


    protected function set_shipment_method($shipment_id)
    {
        $shipping_object = new ViewObject();
        $address = $this->db->where('id', $this->session->userdata('shipping') )->get('nct_addresses')->row();
 
        //oops, no shipping address, lets try billing
        if(!$address)
        {
            $address = $this->db->where('id',$this->session->userdata('billing'))->get('nct_addresses')->row();
        }

        $shipping_object = $this->calc_shipping_by_id( $shipment_id , $address );

        //extension modules can use this event to override shipping cost
        Events::trigger('SHOPEVT_SetShippingMethod', $shipping_object );

        $this->session->set_userdata('shipment_id', $shipping_object->id );
        $this->session->set_userdata('shipping_cost', $shipping_object->cost );
        $this->session->set_userdata('shipping_tax', $shipping_object->tax );    
        $this->session->set_userdata('packing_slip', json_encode($shipping_object->packing_slip) );  

        return $shipping_object;
    }

 }