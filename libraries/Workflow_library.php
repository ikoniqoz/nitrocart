<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Workflow_library extends ViewObject
{
	public function __construct($params = [])
	{
        $this->load->library('nitrocart/Toolbox/Nc_enums');
        parent::__construct();
	}


    public function admin_login($data =NULL)
    {

    }


    public function user_registered($id)
    {

        //
        // Check to see if we need to activate by email
        //
        $red_to = NULL;


        //
        // Where do we want to redirect to afterwards
        //
        if($red_to = $this->session->userdata('nitrocart_redirect_to'))
        {


            // Determine which form of activation is set in the Admin settings
            switch( (int) Settings::get('activation_email') )
            {

                //
                // Send them to a page where they can put in the code
                // Sent to them from PyroCMS system
                // The 'nitrocart_redirect_to' cant be used now, lets keep it in the session 
                //
                case UserActivationMode::ACTIVATE_BY_EMAIL:
                    $red_to = 'users/activate';
                    break;


                //
                // Log them in instantly, Im not sure why Pyro does not auto login
                //
                case UserActivationMode::ACTIVATE_INSTANTLY:
                    $this->session->unset_userdata('nitrocart_redirect_to');
                    $this->load->library('ion_auth');
                    $this->ion_auth->force_login($id, true);
                    break;


                //
                // Inform them that their account will need to be
                // verified by the Admin
                //
                //  Clear the setting
                //  Perhaps set a message
                //
                case UserActivationMode::ACTIVATE_BY_ADMIN:

                    $this->session->unset_userdata('nitrocart_redirect_to');
                    //keep the default action
                    $red_to = NULL; 
                    break;

            }

        }


        if( $red_to != NULL )
        {
            redirect( $red_to );
        }
    }

    public function user_login($data =NULL)
    {
        $this->load->model('nitrocart/products_front_m');


        if($this->session->userdata('shop_force_redirect'))
        {
            $redir = $this->session->userdata('shop_force_redirect');
            $this->session->unset_userdata('shop_force_redirect') ;
            redirect( $redir );
        }

        $user = $this->current_user ? $this->current_user : $this->ion_auth->get_user();

        /**
         * re-init a user if loggin in
         */
        if( ($user) AND ($user->id > 0))
        {

            if( system_installed('feature_permcart') )
            {

                $this->load->model('nitrocart/carts_m');
                $cart_items = (array) $this->mycart->contents();

                foreach($cart_items as $item)
                {
                    //we have items from existing session
                    $this->carts_m->modify($user->id, $item['id'] , $item['productid'], $item['price'], $item['qty'] , $item['options']);
                }

                 if($this->carts_m->has_items($user->id))
                 {

                    $this->session->set_flashdata(JSONStatus::Success,'We found exsisting items from a old session and have added them for you...');

                    //clear the cart
                    $this->mycart->destroy();

                    //re-add all cart items
                    $items = $this->carts_m->get_all_by_user($user->id);
   
                    foreach($items as $item)
                    {
                        if($product = $this->products_front_m->get_product($item->product_id,false))
                        {
                            $variant = $this->db->where('id', $item->variance_id )->get('nct_products_variances')->row();
                            $cart_item = nc_prepare_cart_item( $product , $variant ,  $item->qty , json_decode($item->options) );
                            $this->mycart->insert($cart_item);  
                        }
                    }

                 }
            }
            else
            {
                //echo  "App not installed";die;
            }

        }
        else
        {
            //echo  "Not current user";die; 
        } 
    }

    public function user_account_activate($id)
    {

        if( $redir = $this->session->userdata('nitrocart_redirect_to' ))
        {
            $this->load->library('ion_auth');

            //Ion_auth.php
            $this->ion_auth->force_login($id, true);

            //Unset flag
            $this->session->unset_userdata('nitrocart_redirect_to' );

            redirect( $redir );

        }
    }


}