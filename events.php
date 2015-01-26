<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Events_Nitrocart
{

    static $common_payload_called;


    /**
     * Get the CI instance into this object
     *
     * @param unknown_type $var
     */
    public function __get($var)
    {
        if (isset(get_instance()->$var))
        {
            return get_instance()->$var;
        }
    }

    /**
     * Register event triggers
     */
    public function __construct()
    {
        
        // Register Nitrocart events
        Events::register('evt_order_paid', [$this, 'evt_order_paid'] );
        Events::register('evt_gateway_callback', [$this, 'evt_payment_callback']);
        Events::register('evt_admin_load_assests', array($this, 'evt_admin_load_assests'));
        // Extend built in events
        Events::register('admin_controller', array($this, 'evt_admin_controller'));
        Events::register('public_controller', array($this, 'evt_public_controller'));
        Events::register('post_user_register', array($this, 'evt_post_user_register'));
        Events::register('post_user_activation', array($this, 'evt_post_user_activation'));
        Events::register('post_user_login', array($this, 'evt_user_login'));
        Events::register('post_admin_login', array($this, 'evt_admin_login'));

        // NitroCart Admin Events
        Events::register('SHOPEVT_RegisterModule', array($this, 'shopevt_register_module'));
        Events::register('SHOPEVT_DeRegisterModule', array($this, 'shopevt_de_register_module'));


        //NitroCart Public events
        Events::register('SHOPEVT_OrderPlaced', array($this, 'evt_order_lodged'));
        Events::register('SHOPEVT_SendOrderInvoice', array($this, 'evt_order_resend_invoice'));
        Events::register('SHOPEVT_ShopPublicController', array($this, 'shopevt_ShopPublicController'));
        Events::register('SHOPEVT_ShopAdminController', array($this, 'shopevt_ShopAdminController'));
        Events::register('SHOPEVT_AdminProductListGetFilters', array($this, 'shopevt_AdminProductListGetFilters'));

        Events::register('SHOPEVT_AdminProductGet', [$this, 'shopevt_admin_product_get']);
    }



    /**
     * This is where core/built in features can show tabs for the prouct view
     *
     * @param  [type] $product [description]
     * @return [type]          [description]
     */
    public function shopevt_admin_product_get($product){ }



    /**
     * Get filters for the admin products display
     */
    public function shopevt_AdminProductListGetFilters($filters)
    {
        $all = $this->db->get('nct_products_types')->result();
        $filters->modules["All Products"]['nitrocart,all|0']     = 'All products';
        $filters->modules["All Products"]['nitrocart,price|0']   = 'With price records';
        $filters->modules["All Products"]['nitrocart,noprice|0'] = 'Without price records';
        
        foreach($all as $type)
            $filters->modules["Product Type"]["nitrocart,producttype|{$type->id}"] = $type->name;
    }

    /**
     * Registers a Module with SHOP
     *
     * Expected Array format:
     *
     *   $array_data = array(
     *            'name'=> 'Categories',
     *            'namespace'=>'nitrocart_categories',
     *            'product-tab'=> true|false,
     *   );
     */
    public function shopevt_register_module($module_data=[])
    {
        $this->_register_module($module_data,'install');
    }

    /**
     * De-register a shop module, usually for uninstall
     */
    public function shopevt_de_register_module($module_data=[])
    {
        $this->_register_module($module_data,'uninstall');
    }


    /**
     * helpers for register/deregister
     */
    private function _register_module($module_data=[],$method='install')
    {
        $this->load->model('nitrocart/modules_m');
        $this->modules_m->$method($module_data);
        $this->load->library('nitrocart/routes_library');
        $this->routes_library->rebuild();
    }



    /**
     * Event for user login
     */
    public function evt_user_login()
    {
        $this->shopevt_ShopPublicController();
        $this->load->library('nitrocart/workflow_library');
        $this->workflow_library->user_login();
    }

    /**
     * Login strait to shop dashboard
     *
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function evt_admin_login($data=NULL)
    {
        $this->load->library('nitrocart/workflow_library');
        $this->workflow_library->admin_login($data);
    }

    /**
     * if redir is set redir to that page,
     * therwise redirect to checkout
     *
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function evt_post_user_register($id)
    {
        $this->load->library('nitrocart/workflow_library');
        $this->workflow_library->user_registered($id);
    }

    /**
     * Auto login after succesfull activation
     */
    public function evt_post_user_activation($id)
    {   
        $this->load->library('nitrocart/workflow_library');
        $this->workflow_library->user_account_activate($id);
    }

    /**
     * Common shop admin controller event
     */
    public function shopevt_ShopAdminController($data=[])
    {
        $this->shopevt_ShopPublicController();
        $this->load->helper('nitrocart/nitrocart_admin');
        $this->load->helper('nitrocart/menu');
        $this->template->enable_parser(true);
    }

    /**
     * Common public shop controller ebent
     */
    public function shopevt_ShopPublicController($data=[])
    {
        $this->_common_payload();
        //now just shop specific pages
        $this->template->append_metadata('<script></script>');
    }


    /**
     * Standard public controller
     */
    public function evt_public_controller($input=[])
    {
         $this->_common_payload();
        $this->_call_nitrocart_features_events();         
    }


    /**
     * Required on all pages, standard, admin and shop controllers
     */
    private function _common_payload($is_admin=false)
    {

        //do not want to recvall this stuff
        if(self::$common_payload_called)
        {
            return;
        }

        $this->load->library('nitrocart/nitrocore_library'); 
        Asset::add_path('nitrocart', NITROCART_INSTALL_PATH ); 

        $this->load->library('nitrocart/mycart'); 
        $this->load->library('nitrocart/Toolbox/Nc_tools');   
        $this->load->helper('nitrocart/nitrocart'); 
        $this->lang->load('nitrocart/nitrocart');


        self::$common_payload_called = TRUE;


        //exit
        if($is_admin) return;

        //use x:js file='common'
        //$this->template->append_js('nitrocart::public/common.js');    

        $js = PHP_EOL;
        $js.= '<!-- Globals -->'.PHP_EOL;
        $js.= '<script type="text/javascript">'.PHP_EOL;
        $js.= '    var APPPATH_URI                 = "'.APPPATH_URI.'";'.PHP_EOL;
        $js.= '    var SITE_URL                    = "'.rtrim(site_url(), '/').'/";'.PHP_EOL;
        $js.= '    var BASE_URL                    = "'.BASE_URL.'";'.PHP_EOL;
        $js.= '    var BASE_URI                    = "'.BASE_URI.'";'.PHP_EOL;
        $js.= '    var STORE_ID                    = "'.$this->_get_hash().'";'.PHP_EOL;
        $js.= '    var X_URI                       = "'.rtrim(site_url(), '/').'/'.NC_ROUTE.'";'.PHP_EOL;
        $js.= '    var NC_ROUTE                    = "'.NC_ROUTE.'";'.PHP_EOL;

        $js.= '</script>';

        $this->template->append_metadata($js);

    }


    private function _get_hash()
    {
        return md5(
            site_url().
            $this->session->userdata('http_server').
            $this->input->server('SERVER_NAME').
            $this->input->server('SERVER_ADDR').
            $this->input->server('SERVER_SIGNATURE')
            );      
    }





    /**
     * Send Admin and User Email notification that order has been placed
     * @param  array   $email_variables [description]
     * @param  boolean $is_guest        [description]
     * @return [type]                   [description]
     */
    public function evt_order_lodged( $email_variables=[], $is_guest = false )
    {
        $this->load->library('nitrocart/email_library');
        $admin_email = Settings::get('shop_order_notify_email');
        $email_variables =  $this->email_library->prepareOrderLodgedEmail( $email_variables );
        $this->email_library->sendEmail( $email_variables, 'shop_user_order_notification', $email_variables['email'] );
        if($admin_email != '')
            $this->email_library->sendEmail( $email_variables, 'shop_admin_order_notification' , $admin_email );
    }

    /**
     * Resend invoice event
     */
    public function evt_order_resend_invoice( $order_id )
    {
        $this->load->library('nitrocart/email_library');
        $email_variables =  $this->email_library->prepareOrderInvoiceEmail( $order_id );
        $this->email_library->sendEmail( $email_variables, 'nct_order_invoice', $email_variables['email'] );
    }

    // Send Admin and User Email notification that order has been placed
    public function evt_order_paid($id)
    {
        $this->load->library('nitrocart/email_library');
        $email_variables =  $this->email_library->prepareOrderPaidEmail( $id );
        $admin_email = Settings::get('shop_payment_notify_email');
        $this->email_library->sendEmail( $email_variables, 'shop_user_order_paid' , $email_variables['email'] );
        if($admin_email != '')
            $this->email_library->sendEmail( $email_variables, 'shop_admin_order_paid' , $admin_email );
    }

    // Send User email notification thah payment was received
    public function evt_gateway_callback($input=[])
    {
        return;
    }


    /**
     * Call the commen_events from the features file
     */
    private function _call_nitrocart_features_events()
    {
        $this->load->model('nitrocart/systems_m');

        $installed_features = $this->systems_m->get_installed_features();
        
        $args = null;

        foreach($installed_features as $module)
        {
            $this->load->library('nitrocart/features/'.$module->driver);
            
            $this->{$module->driver}->event_common($args);
        }
    }

}
/* End of file events.php */