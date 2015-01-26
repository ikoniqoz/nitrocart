<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class System_z_admin_layer  extends Store_module 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');   
        $this->config->load('nitrocart/install/' . NC_CONFIG);   
    }


    /**
     * Install step10
     * @param  [type] $installer [description]
     * @return [type]            [description]
     */
    public function install($installer=NULL)
    {

        /**
         * Is this already installed ? ... move on
         */
        if($installer->is_installed('System_z_admin_layer')) return true;


        $this->install_e();



        //we do not call uninstall on this instance as we dont, or may not have a routebackup file

        /**
         * Install the routes directvies
         */
        $this->load->model('nitrocart/admin/routes_admin_m');


        /**
         * Truncate data
         */
        $this->routes_admin_m->do_truncate();



        foreach($this->get_routes() as $route)
        {
            $this->routes_admin_m->create($route,'core');
        }


        /**
         * rebuild the routes to finalize installation
         */
        $this->load->library('nitrocart/routes_library');
        $this->routes_library->nitrocart_install();           


        // Install the menu DB content
        $this->db->insert_batch('nct_admin_menu', $this->_get_admin_menu_data() );
        
        set_time_limit ( 60 );

        return true;
    }

    private function install_e()
    {
        /**
         * 
         */
        if($this->config->item('install/default_countries')) $this->db->insert_batch('nct_countries', $this->getCountries() );



        /**
         * Install default packages
         */
        if($this->config->item('install/default_packages')) $this->installDefaultPackages();
  


        /**
         * Install some attributes
         */
        $this->install_attributes();



        /**
         * Setup core product type
         */
        $this->installProduct_type();
        


        /**
         * Setup default tax record
         */
        if($this->config->item('install/default_taxes')) $this->install_taxes();
        





        /**
         * Setup default gateway
         * @var [type]
         */
        if($this->config->item('install/default_gateway')) $this->install_default_gateway();
        




        


        /**
         * Install default shipping
         */
        if($this->config->item('install/default_shipping')) $this->install_default_shipping();







        /**
         * Finally lets install some product data 
         * oh, only for dev and testing of course...
         */
        $this->sample_product_data();





        //install default workflows
        $this->install_default_workflows();





        // Insert Email template
        $em_tmp = $this->get_email_templates();
        $this->db->insert_batch('email_templates', $em_tmp);



        // Insert settings
        $settings = $this->get_settings();
        $this->db->insert_batch('settings', $settings);

        return true;
    }


    private function uninstall_e()
    {
        if($this->db->table_exists('nct_admin_menu'))
        {  
            $this->db->truncate('nct_admin_menu');
        }

        if($this->db->table_exists('nct_countries'))
        {  
            $this->db->truncate('nct_countries');
        }

        if($this->db->table_exists('nct_packages'))
        {  
            $this->db->truncate('nct_packages');
        }

        if($this->db->table_exists('nct_packages_groups'))
        {  
            $this->db->truncate('nct_packages_groups');
        }

        if($this->db->table_exists('nct_products'))
        {  
            $this->db->truncate('nct_products');
        }

        if($this->db->table_exists('nct_products_variances'))
        {  
            $this->db->truncate('nct_products_variances');
        }

        if($this->db->table_exists('nct_products_types'))
        {  
            $this->db->truncate('nct_products_types');
        }

        if($this->db->table_exists('nct_checkout_options'))
        {  
            $this->db->truncate('nct_checkout_options');
        }

        if($this->db->table_exists('nct_tax'))
        {  
            $this->db->truncate('nct_tax');
        }

        if($this->db->table_exists('nct_attributes'))
        {  
            $this->db->truncate('nct_attributes');
        }        

        if($this->db->table_exists('nct_e_attributes'))
        {  
            $this->db->truncate('nct_e_attributes');
        }   

        if($this->db->table_exists('nct_workflows'))
        {  
            $this->db->truncate('nct_workflows');
        }  

        $this->db->delete('settings', ['module' => 'nitrocart']);

        $this->db->delete('email_templates', ['module' => 'nitrocart']);       
        
        return true; 
    }


    public function uninstall($installer=NULL)
    {

        /*
         * Load the routes library,models
         */
        $this->load->library('nitrocart/routes_library');    
        $this->load->model('nitrocart/admin/routes_admin_m');


        /*
         * Truncate data
         */
        if($this->db->table_exists('nct_routes'))
        {
            $this->routes_admin_m->do_truncate();
        }



        /*
         * Restore the original routes file if we can find it
         */
        if( $this->routes_library->nitrocart_uninstall() )
        {      
            $this->uninstall_e();

            return true;                
        }

        return false;
    }


    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }

    private function get_routes()
    {
        $core_routes =  
        [
            [
                    'name'  => 'NitroCart Common',
                    'uri'   => '/(index|cart|eavcart|checkout|closed)(:any)?',
                    'dest'  => $this->config->item('core/path').'/$1$2'
            ],
            [
                    'name'  => 'NitroCart Payments',
                    'uri'   => '/payment/(order|process|rc|cancel|callback)(/:num)?',
                    'dest'  => $this->config->item('core/path').'/payment/$1$2'
            ],                                
            [
                    'name'  => 'NitroCart Products List',
                    'uri'   => '/products(/:num)?',
                    'dest'  => $this->config->item('core/path').'/products/index$1'
            ],
            [
                    'name'  => 'NitroCart Products Details',
                    'uri'   => '/products(/:any)?',
                    'dest'  => $this->config->item('core/path').'/products$1'
            ],    /*                           
            [
                    'name'  => 'NitroCart Products Details',
                    'uri'   => '/products/product(/:any)?',
                    'dest'  => $this->config->item('core/path').'/products/product$1'
            ],  */                              
            [
                    'name'  => 'NitroCart Store Home Page',
                    'uri'   => '',
                    'dest'  => 'pages', // $this->config->item('core/path').''  // //$route['store'] = 'pages'; //'nitrocart';
            ]
           
        ];  

        return $core_routes;     
    }    

    private function _get_admin_menu_data()
    {
        return [
                [
                    'label'         => 'lang:nitrocart:admin:dashboard',
                    'uri'           => NC_ADMIN_ROUTE.'/dashboard',
                    'menu'          => 'lang:nitrocart:admin:shop',
                    'module'        => 'nitrocart',
                    'order'         => 1,
                    'visible'       => 1,
                ],
                [
                    'label'         => 'lang:nitrocart:admin:orders',
                    'uri'           => NC_ADMIN_ROUTE.'/orders',
                    'menu'          => 'lang:nitrocart:admin:shop',
                    'module'        => 'nitrocart',
                    'order'         => 2,
                    'visible'       => 1,                
                ],
                [
                    'label'         => 'lang:nitrocart:admin:products',
                    'uri'           => NC_ADMIN_ROUTE.'/products',
                    'menu'          => 'lang:nitrocart:admin:shop',
                    'module'        => 'nitrocart',
                    'order'         => 3,
                    'visible'       => 1,                
                ],
                [
                    'label'         => 'lang:nitrocart:admin:gateways',
                    'uri'           => NC_ADMIN_ROUTE.'/gateways',
                    'menu'          => 'lang:nitrocart:admin:shop_admin',
                    'module'        => 'nitrocart',
                    'order'         => 1,
                    'visible'       => 1,                
                ],
                [
                    'label'         => 'lang:nitrocart:admin:shipping',
                    'uri'           => NC_ADMIN_ROUTE.'/shipping',
                    'menu'          => 'lang:nitrocart:admin:shop_admin',
                    'module'        => 'nitrocart',
                    'order'         => 3,
                    'visible'       => 1,                
                ],
                [
                    'label'         => 'lang:nitrocart:admin:shipping_packages',
                    'uri'           => NC_ADMIN_ROUTE.'/packages',
                    'menu'          => 'lang:nitrocart:admin:shop_admin',
                    'module'        => 'nitrocart',
                    'order'         => 5,
                    'visible'       => 1,                
                ],
                [
                    'label'         => 'lang:nitrocart:admin:shipping_countries',
                    'uri'           => NC_ADMIN_ROUTE.'/countries ',
                    'menu'          => 'lang:nitrocart:admin:shop_admin',
                    'module'        => 'nitrocart',
                    'order'         => 10,
                    'visible'       => 1,                
                ],
                [
                    'label'         => 'lang:nitrocart:admin:customers',
                    'uri'           => NC_ADMIN_ROUTE.'/customers',
                    'menu'          => 'lang:nitrocart:admin:shop_admin',
                    'module'        => 'nitrocart',
                    'order'         => 30,
                    'visible'       => 1,                
                ],
                [
                    'label'         => 'lang:nitrocart:admin:manage',
                    'uri'           => NC_ADMIN_ROUTE.'/manage',
                    'menu'          => 'lang:nitrocart:admin:shop_admin',
                    'module'        => 'nitrocart',
                    'order'         => 100,
                    'visible'       => 1,                
                ]
            ];
    }


    /**
     * Install products
     */
    private function sample_product_data()
    {

        $sample_products = $this->config->item('install/sample_products_data');

        $this->load->model('nitrocart/admin/products_admin_m');
        $this->load->model('nitrocart/admin/products_variances_admin_m');

        //can only do if we have a package id
        if($package_id = $this->db->get('nct_packages_groups')->row())
        {
            foreach($sample_products as $name => $product_data)
            {
                if($type = $this->db->where('slug',$product_data['type'])->get('nct_products_types')->row())
                {
                    $product = 
                    [
                        'name'              => $name,
                        'type_id'           => $type->id,
                        'public_override'   => 1,
                        'type_slug'         => $type->slug,
                        'pkg_group_id'      => $package_id->id,
                        'price'             => $product_data['price'],
                    ];
                    $this->products_admin_m->create($product);
                }
            } 
        } 
    }



    /**
     * [installDefaultPackages description]
     * @return [type] [description]
     */
    private function installDefaultPackages()
    {

        // Insert Default  Package Group
        $data = 
        [
            'name'      => 'Default Package Group', 
            'created_by'=> $this->current_user->id, 
            'created'   => date("Y-m-d H:i:s") 
        ];
        $gid = $this->db->insert('nct_packages_groups', $data);

        $this->session->set_userdata('install_package_id',$gid);


        $input = 
        [
            'name'          => "Default Package",
            'pkg_group_id'  => $gid,
            'height'        => 30,
            'width'         => 15,
            'length'        => 40,
            'outer_height'  => 30,
            'outer_width'   => 15,
            'outer_length'  => 40,
            'max_weight'    => 10,
            'cur_weight'    => 0.15,
        ];

        //create the default package
        $this->load->model('nitrocart/admin/packages_admin_m');
        $this->packages_admin_m->create( $input ,1);        
    }



    /**
     * [installProduct_type description]
     * @return [type] [description]
     */
    private function installProduct_type()
    {    
        $this->load->model('nitrocart/admin/products_types_admin_m'); 
        $types          = $this->config->item('install/default_product_types');
        foreach($types as $type_name => $type_data)
        {
            $core = isset($type_data['core'])?1:0;
            $the_attributes = $type_data['attributes'];
            $to_insert = [];
            $to_insert['name']          = $type_name;
            $to_insert['default']       = $type_data['default'];
            $type_id = $this->products_types_admin_m->create( $to_insert, $this->prep($the_attributes) , $core  ); 
        }  
    }

    private function prep($the_attributes)
    {
        $att_props = [];
        $this->load->model('nitrocart/admin/attributes_m');
        foreach($the_attributes as $key => $name)
        {
            if($row = $this->db->where('name',$name)->get('nct_attributes')->row())
            {
                $att_props[] = $row;
            }
            else
            {   
                //second attempt
                $id = $this->create_attribute_single($name);
                if($row = $this->db->where('id',$id)->get('nct_attributes')->row())
                {
                     $att_props[] = $row;
                }
            }
        }
        return $att_props;
    }

    /**
     * [install_attributes description]
     * @return [type] [description]
     */
    private function install_attributes()
    {
        $this->load->library('nitrocart/Toolbox/Nc_string'); 
        if($attributes = $this->config->item('install/default_product_attributes'))
        {
            $attributes = new NCString($attributes);
            $array = $attributes->toArray(StringSplitOptions::DelimitPipe);
            $to_insert = [];
            foreach($array as $item)
            {
                $attribte = trim(strtolower($item));
                if($attribte != '')
                {
                    $to_insert[] = 
                    [
                        'name'  => $item,
                        'slug'  => trim(strtolower($item)),
                    ];
                }
            }
            $this->db->insert_batch('nct_attributes',$to_insert);   
        }    
    }

    private function create_attribute_single($name)
    {
        $this->load->library('nitrocart/Toolbox/Nc_string'); 
        $this->load->model('nitrocart/admin/attributes_m'); 

        $id = $this->attributes_m->create(['name'=>$name]);   
        return $id;
    } 

   /**
     * Convert the country list array into a format that can be injected into the DB.
     *
     */
    private function getCountries()
    {
        $retarray = [];

        foreach ($this->new_countryList as $region => $sublist) 
        {
            foreach ($sublist as $code => $name) 
            {
                $retarray[] = ['code2' => $code, 'name' => $name,  'region' => $region ];
            }
        }

        //insert countries
        $__insert_date__ = date("Y-m-d H:i:s");
        $data = [];
        foreach( $retarray as $key => $value)
        {
            $data[] = 
                [
                    'name'          => $value['name'],
                    'code2'         => $value['code2'],
                    'code3'         => '',
                    'region'        => $value['region'],
                    'enabled'       => 1,
                    'created_by'    => $this->current_user->id,
                    'created'       => $__insert_date__,
                ];
        }

        return $data;
    }



    /**
     * [install_taxes description]
     * @return [type] [description]
     */
    private function install_taxes()
    {
        $taxes = [
            [
                'name'          => 'No TAX',
                'deleted'       => NULL,
                'rate'          => 0,
                'default'       => 1, 
                'created'       => date("Y-m-d H:i:s"),
            ],
            [
                'name'          => '10 Percent',
                'deleted'       => NULL,
                'rate'          => 10,
                'default'       => 0, 
                'created'       => date("Y-m-d H:i:s"),                
            ],            

        ]; 

        return $this->db->insert_batch('nct_tax', $taxes);
    }


    /**
     * [install_default_gateway description]
     * @return [type] [description]
     */
    private function install_default_gateway()
    {
        $insert = [
                'title' => 'Bank',
                'slug' => 'manual',
                'description' => 'Manual payment method.',
                'enabled' => 1, 
                'options' => '',
                'created_by'    => $this->current_user->id,
                'created'       => date("Y-m-d H:i:s"),
                'updated'       => date("Y-m-d H:i:s"),
                'module_type'   => 'gateway',
        ]; 

        $this->db->insert('nct_checkout_options', $insert);
    }

    /**
     * [install_default_shipping description]
     * @return [type] [description]
     */
    private function install_default_shipping()
    {
        $insert = [
                'title' => 'Flat Rate',
                'slug' => 'flatrate',
                'description' => 'Flat rate Shipping',
                'enabled' => 1, 
                'options' => 'a:2:{s:6:"amount";s:1:"0";s:8:"tax_rate";s:4:"0.00";}',
                'created_by'    => $this->current_user->id,
                'created'       => date("Y-m-d H:i:s"),
                'updated'       => date("Y-m-d H:i:s"),
                'module_type'   => 'shipping',
        ]; 

        $this->db->insert('nct_checkout_options', $insert);
    }



    private function get_email_templates()
    {

        return [
            [
                'slug' => 'shop_gift_certificate',
                'name' => 'Store: Customer [Gift Certificate]',
                'description' => 'This email will be sent to a client for purchasing a gift certificate',
                'subject' => 'Gift Certificate',
                'body' => '<h1>You have received a gift certificate from {{gift_from}}</h1>
                    <b>Value:</b> {{ value }}<br />
                    <b>Balance:</b> {{ balance }}<br />',
                'lang' => 'en',
                'is_default' => 1,
                'module' => 'nitrocart'
            ],
            [            
                'slug' => 'shop_admin_order_notification',
                'name' => 'Store: Admin [Order Notification]',
                'description' => 'This email will be sent to Administrators when new orders are submitted',
                'subject' => 'A new order has been submitted',
                'body' => '<h1>An order has just been submitted on your online store</h1>
                    <b>Order ID:</b> {{ order_id }}<br />
                    <b>Order Date:</b> {{ order_date }}<br />
                    <b>IP Address:</b> {{ customer_ip }}<br /><br />
                    <p><b>Order Total:</b>{{ nitrocart:currency }} {{ cost_total }}</p><br />
                    <p><a href="{{ url:site }}'.NC_ADMIN_ROUTE.'/orders/order/{{ order_id }}">view full order details online</a></p>
                    <p>{{ order_contents }}</p>
                    ',
                'lang' => 'en',
                'is_default' => 1,
                'module' => 'nitrocart'
            ],
            [      
                //shop_user_order_notification
                'slug' => 'shop_user_order_notification',
                'name' => 'Store: Customer [Order Notification]',
                'description' => 'Email sent to user when order is submitted',
                'subject' => '{{ settings:shop_name }} - Order Confirmation',
                                'body' => '<p>Hi {{ first_name }} {{ last_name }}</p>
                                        <p>Thank you for placing your order with {{ settings:shop_name }}</p>
                                        <p>This email is to inform you of the details of your order and where you can check the status of your order.</p>
                                        <p>Once we have received full payment, your order will be prepared for delivery</p>
                    <p><b>Order ID No:</b> {{ order_id }}<br />
                    <b>Order Date:</b> {{ order_date }}<br />
                    <b>Order Total:</b>{{ nitrocart:currency }} {{ cost_total }}</p>
                    <p>To view your order, you may need to <a href="{{ url:site }}users/login">login to your online account</a> so that you can
                    <a href="{{ url:site }}'.NC_ROUTE.'/my/orders/order/{{ order_id }}">view your order status.</a></p>',

                'lang' => 'en',
                'is_default' => 1,
                'module' => 'nitrocart'
            ],
            [      
                //shop_user_order_paid
                'slug' => 'shop_user_order_paid',
                'name' => 'Store: Customer [Order Paid Notification]',
                'description' => 'Email sent to user when order is paid',
                'subject' => '{{ settings:shop_name }} - Order has been paid',
                'body' => '<p>Hi {{ first_name }},</p>
                    <p>
                        Thank you for your payment. To view your order, you may need to <a href="{{ url:site }}'.NC_ROUTE.'/my/">login to your online account</a> so that you can 
                        <a href="{{ url:site }}'.NC_ROUTE.'/my/orders/order/{{ order_id }}">view your full order details.</a>
                    </p>
                    ',
                'lang' => 'en',
                'is_default' => 1,
                'module' => 'nitrocart'
            ],
            [      
                'slug' => 'shop_admin_order_paid',
                'name' => 'Store: Admin [Order Paid Notification]',
                'description' => 'Email sent to Admin when order is paid',
                'subject' => 'Order # {{ order_id }} - has been paid',
                'body' => '<p>Hi there! <br></p>
                    <p>
                        This email is to let you know that an order has been paid, you should received funds shortly.
                    </p>',
                'lang' => 'en',
                'is_default' => 1,
                'module' => 'nitrocart'
            ],
            [      
                'slug' => 'nct_order_invoice',
                'name' => 'Store: Invoice',
                'description' => 'Invoice/Email sent to user when order is placed, or manually sent by Administrator',
                'subject' => 'Invoice #{{ order_id }} - [{{ settings:shop_name }}]',
                'body' => '<p>Hi {{ first_name }},</p>
                    <p>
                        If you have paid for this order, please ignor this email or contact our sales department
                        To view your order in full, navigate to your <a href="{{ url:site }}'.NC_ROUTE.'/my/"> online account</a> so that you can 
                        <a href="{{ url:site }}'.NC_ROUTE.'/my/orders/order/{{ order_id }}">view your full account details.</a>
                    </p>
                    <ul>
                        <li>Shipping and Handling : $ {{amt_shipping_total}} </li>
                        <li>$ {{amt_items_total}} - total amount for items purchased</li>
                        <li>TAX : $ {{amt_tax}}</li>
                        <li>Order Total (tax inc) $: {{amt_item_total_ic_tax}}</li>
                        <li>Invoice ID:{{order_id}}</li>
                        <li>Order ID:{{order_id}}</li>
                        <li>Date Order Placed:  {{order_date}}</li>
                    </ul>
                    <h3>Items Purchased</h3>
                    {{ item_list }} 
                        {{qty}} 
                        {{title}}
                        {{amt}}
                    {{ /item_list }}',

                'lang' => 'en',
                'is_default' => 1,
                'module' => 'nitrocart'
            ],
        ];
    }

    /**
     * Settings
     * @return [type] [description]
     */
    private function get_settings()
    {

    
        $settings = [
            [
                'slug' => 'shop_store_type',
                'title' => 'Store Type',
                'description' => 'What type of store would you like, a Showcase store will not allow you to checkout and purchase items.',
                'type' => 'select',
                'default' => 'standard',
                'value' => '',
                'options' => 'showcase=Showcase|standard=Store',
                'is_required' => true,
                'is_gui' => true,
                'module' => 'nitrocart',
                'order' => 200
            ],
            [
                'slug' => 'shop_allow_guest_checkout',
                'title' => 'Allow Guest Checkout',
                'description' => 'Will you allow guests to purchase online..',
                'type' => 'select',
                'default' => 0,
                'value' => '',
                'options' => '1=Yes please|0=No thanks',
                'is_required' => false,
                'is_gui' => true,
                'module' => 'nitrocart',
                'order' => 200
            ],
            [
                'slug' => 'shop_checkout_method',
                'title' => 'Checkout Method',
                'description' => 'Here you can select between the 3 step and 7 step checkout system. By default 3 step is enabled.',
                'type' => 'select',
                'default' => 'threestep',
                'value' => 'threestep',
                'options' => 'threestep=Three Step Checkout|multistep=Seven Step Checkout',
                'is_required' => false,
                'is_gui' => true,
                'module' => 'nitrocart',
                'order' => 190
            ],
            [
                'slug' => 'shop_ssl_required',
                'title' => 'Enable Secure SSL Payment',
                'description' => 'Require to proccess order and payment through SSL',
                'type' => 'select',
                'default' => 0,
                'value' => '',
                'options' => '1=Yes please|0=No thanks',
                'is_required' => false,
                'is_gui' => true,
                'module' => 'nitrocart',
                'order' => 180
            ],
            [
                'slug' => 'shop_currency_mode',
                'title' => 'Currency Mode',
                'description' => 'Select which currency format',
                'type' => 'select',
                'default' => '0',
                'value' => '0',
                'options' => '0= AUD &#36; 1,000.00 (Australia) | 1= EURO &#128; 1.000,00 (Europe) | 2= USD &#36; 1,000.00 (USA)',
                'is_required' => false,
                'is_gui' => true,
                'module' => 'nitrocart',
                'order' => 160
            ],
            [
                'slug' => 'shop_host_provider',                
                'title' => 'Host provider details',
                'description' => 'If you are reselling NitroCart please enter your support contact details here.',
                'type' => 'text',
                'default' => 'Inspired Technology Australia (2014)',
                'value' => 'Inspired Technology Australia (2014)',
                'options' => '',
                'is_required' => true,
                'is_gui' => true,
                'module' => 'nitrocart',
                'order' => 150
            ],
            
            //
            //
            // hidden
            //
            //

            [
                'slug' => 'shop_name',                  
                'title' => 'Shop Name',
                'description' => 'Give your online store a name - This will be used on title pages and general places around the Shop',
                'type' => 'text',
                'default' => $this->config->item('install/default_store_name'), 
                'value' =>  $this->config->item('install/default_store_name'),
                'options' => '',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 20
            ],
            [
                'slug' => 'shop_open_status',                   
                'title' => 'Shop Open Status',
                'description' => 'Use this option to the user-facing part of the Shop. Useful when you want to take the Shop offline without shutting down the whole site',
                'type' => 'radio',
                'default' => $this->config->item('install/default_store_open'),
                'value' => $this->config->item('install/default_store_open'), 
                'options' => '1=Open|0=Closed',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 30
            ],
            [
                'slug' => 'shop_closed_reason',   
                'title' => 'SHOP Close Message',
                'description' => 'This is the public message you want to display to your customers as to why the SHOP is closed.',
                'type' => 'textarea',
                'default' => 'We are closed for maintenance',
                'value' => 'We are closed for maintenance',
                'options' => '',
                'is_required' => false,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 40
            ],
            [
                'slug' => 'shop_payment_notify_email',   
                'title' => 'Payment Notification',
                'description' => 'Enter the email address for admin Payment notification (leave blank for no email)',
                'type' => 'text',
                'default' => $this->config->item('install/default_payment_notification_email'),
                'value' => $this->config->item('install/default_payment_notification_email'),
                'options' => '',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 60
            ],
            [
                'slug' => 'shop_order_notify_email',   
                'title' => 'Order Notification',
                'description' => 'Enter the email address for admin Order notification (leave blank for no email)',
                'type' => 'text',
                'default' => $this->config->item('install/default_order_notification_email'),
                'value' => $this->config->item('install/default_order_notification_email'),
                'options' => '',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 70
            ],
            [
                'slug' => 'shop_order_overdue_term',   
                'title' => 'Overdue Term',
                'description' => 'Select the overdue payment term (in days)',
                'type' => 'select',
                'default' => '1',
                'value' =>  '1',
                'options' => '1=1 Day|3=3 Days|7=7 Days|14=14 Days|28=28 Days',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 80
            ],
            [
                'slug' => 'shop_date_format',   
                'title' => 'Date Format',
                'description' => 'For both frontend and backend  - (Samples are showing the 28th April 2013)',
                'type' => 'select',
                'default' => '1',
                'value' =>  '1',
                'options' => '0=28-4-2013|1=28/4/2013|2=4-28-2013|3=4/28/2013',
                'is_required' => false,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 90
            ],
            [
                'slug' => 'shop_qty_perpage_limit_front',   
                'title' => 'Products per page (public site)',
                'description' => 'How many products show in list view ',
                'type' => 'text',
                'default' => $this->config->item('install/default_store_perpage_limit'),
                'value' => $this->config->item('install/default_store_perpage_limit'),
                'options' => '',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 120
            ],


        ];

        return $settings;
    }  



    public function install_default_workflows()
    {
        $data = 
        [
            [
                'name'         => 'Pending',
                'section'       => '',
                'core'          => 1,
                'pcent'         => 5, 
                'require'       => 0
            ],
            [
                'name'         => 'Ready for Shipping',
                'section'       => '',
                'core'          => 1,
                'pcent'         => 40, 
                'require'       => 0
            ],
            [
                'name'         => 'Complete',
                'section'       => '',
                'core'          => 1,
                'pcent'         => 50, 
                'require'       => 0
            ],
            [
                'name'         => 'Closed',
                'section'       => '',
                'core'          => 1,
                'pcent'         => 100, 
                'require'       => 0
            ],
            [
                'name'         => 'Re-Opened',
                'section'       => '',
                'core'          => 1,
                'pcent'         => 36, 
                'require'       => 0
            ]
        ]; 

        return $this->db->insert_batch('nct_workflows', $data);
    }

    public $new_countryList = [

                    "Africa" => 
                    [
                        "DZ" => "Algeria",
                        "AO" => "Angola",
                        "BJ" => "Benin",
                        "BW" => "Botswana",
                        "BF" => "Burkina Faso",
                        "BI" => "Burundi",
                        "CM" => "Cameroon",
                        "CV" => "Cape Verde",
                        "CF" => "Central African Republic",
                        "TD" => "Chad",
                        "KM" => "Comoros",
                        "CG" => "Congo - Brazzaville",
                        "CD" => "Congo - Kinshasa",
                        "CI" => "Côte d’Ivoire",
                        "DJ" => "Djibouti",
                        "EG" => "Egypt",
                        "GQ" => "Equatorial Guinea",
                        "ER" => "Eritrea",
                        "ET" => "Ethiopia",
                        "GA" => "Gabon",
                        "GM" => "Gambia",
                        "GH" => "Ghana",
                        "GN" => "Guinea",
                        "GW" => "Guinea-Bissau",
                        "KE" => "Kenya",
                        "LS" => "Lesotho",
                        "LR" => "Liberia",
                        "LY" => "Libya",
                        "MG" => "Madagascar",
                        "MW" => "Malawi",
                        "ML" => "Mali",
                        "MR" => "Mauritania",
                        "MU" => "Mauritius",
                        "YT" => "Mayotte",
                        "MA" => "Morocco",
                        "MZ" => "Mozambique",
                        "NA" => "Namibia",
                        "NE" => "Niger",
                        "NG" => "Nigeria",
                        "RW" => "Rwanda",
                        "RE" => "Réunion",
                        "SH" => "Saint Helena",
                        "SN" => "Senegal",
                        "SC" => "Seychelles",
                        "SL" => "Sierra Leone",
                        "SO" => "Somalia",
                        "ZA" => "South Africa",
                        "SD" => "Sudan",
                        "SZ" => "Swaziland",
                        "ST" => "São Tomé and Príncipe",
                        "TZ" => "Tanzania",
                        "TG" => "Togo",
                        "TN" => "Tunisia",
                        "UG" => "Uganda",
                        "EH" => "Western Sahara",
                        "ZM" => "Zambia",
                        "ZW" => "Zimbabwe",
                    ],
                    "Americas" => 
                    [
                        "AI" => "Anguilla",
                        "AG" => "Antigua and Barbuda",
                        "AR" => "Argentina",
                        "AW" => "Aruba",
                        "BS" => "Bahamas",
                        "BB" => "Barbados",
                        "BZ" => "Belize",
                        "BM" => "Bermuda",
                        "BO" => "Bolivia",
                        "BR" => "Brazil",
                        "VG" => "British Virgin Islands",
                        "CA" => "Canada",
                        "KY" => "Cayman Islands",
                        "CL" => "Chile",
                        "CO" => "Colombia",
                        "CR" => "Costa Rica",
                        "CU" => "Cuba",
                        "DM" => "Dominica",
                        "DO" => "Dominican Republic",
                        "EC" => "Ecuador",
                        "SV" => "El Salvador",
                        "FK" => "Falkland Islands",
                        "GF" => "French Guiana",
                        "GL" => "Greenland",
                        "GD" => "Grenada",
                        "GP" => "Guadeloupe",
                        "GT" => "Guatemala",
                        "GY" => "Guyana",
                        "HT" => "Haiti",
                        "HN" => "Honduras",
                        "JM" => "Jamaica",
                        "MQ" => "Martinique",
                        "MX" => "Mexico",
                        "MS" => "Montserrat",
                        "AN" => "Netherlands Antilles",
                        "NI" => "Nicaragua",
                        "PA" => "Panama",
                        "PY" => "Paraguay",
                        "PE" => "Peru",
                        "PR" => "Puerto Rico",
                        "BL" => "Saint Barthélemy",
                        "KN" => "Saint Kitts and Nevis",
                        "LC" => "Saint Lucia",
                        "MF" => "Saint Martin",
                        "PM" => "Saint Pierre and Miquelon",
                        "VC" => "Saint Vincent and the Grenadines",
                        "SR" => "Suriname",
                        "TT" => "Trinidad and Tobago",
                        "TC" => "Turks and Caicos Islands",
                        "VI" => "U.S. Virgin Islands",
                        "US" => "United States",
                        "UY" => "Uruguay",
                        "VE" => "Venezuela",
                    ],
                    "Asia" => 
                    [
                        "AF" => "Afghanistan",
                        "AM" => "Armenia",
                        "AZ" => "Azerbaijan",
                        "BH" => "Bahrain",
                        "BD" => "Bangladesh",
                        "BT" => "Bhutan",
                        "BN" => "Brunei",
                        "KH" => "Cambodia",
                        "CN" => "China",
                        "CY" => "Cyprus",
                        "GE" => "Georgia",
                        "HK" => "Hong Kong SAR China",
                        "IN" => "India",
                        "ID" => "Indonesia",
                        "IR" => "Iran",
                        "IQ" => "Iraq",
                        "IL" => "Israel",
                        "JP" => "Japan",
                        "JO" => "Jordan",
                        "KZ" => "Kazakhstan",
                        "KW" => "Kuwait",
                        "KG" => "Kyrgyzstan",
                        "LA" => "Laos",
                        "LB" => "Lebanon",
                        "MO" => "Macau SAR China",
                        "MY" => "Malaysia",
                        "MV" => "Maldives",
                        "MN" => "Mongolia",
                        "MM" => "Myanmar [Burma]",
                        "NP" => "Nepal",
                        "NT" => "Neutral Zone",
                        "KP" => "North Korea",
                        "OM" => "Oman",
                        "PK" => "Pakistan",
                        "PS" => "Palestinian Territories",
                        "YD" => "People's Democratic Republic of Yemen",
                        "PH" => "Philippines",
                        "QA" => "Qatar",
                        "SA" => "Saudi Arabia",
                        "SG" => "Singapore",
                        "KR" => "South Korea",
                        "LK" => "Sri Lanka",
                        "SY" => "Syria",
                        "TW" => "Taiwan",
                        "TJ" => "Tajikistan",
                        "TH" => "Thailand",
                        "TL" => "Timor-Leste",
                        "TR" => "Turkey",
                        "TM" => "Turkmenistan",
                        "AE" => "United Arab Emirates",
                        "UZ" => "Uzbekistan",
                        "VN" => "Vietnam",
                        "YE" => "Yemen",
                    ],
                    "Europe" => 
                    [
                        "AL" => "Albania",
                        "AD" => "Andorra",
                        "AT" => "Austria",
                        "BY" => "Belarus",
                        "BE" => "Belgium",
                        "BA" => "Bosnia and Herzegovina",
                        "BG" => "Bulgaria",
                        "HR" => "Croatia",
                        "CY" => "Cyprus",
                        "CZ" => "Czech Republic",
                        "DK" => "Denmark",
                        "DD" => "East Germany",
                        "EE" => "Estonia",
                        "FO" => "Faroe Islands",
                        "FI" => "Finland",
                        "FR" => "France",
                        "DE" => "Germany",
                        "GI" => "Gibraltar",
                        "GR" => "Greece",
                        "GG" => "Guernsey",
                        "HU" => "Hungary",
                        "IS" => "Iceland",
                        "IE" => "Ireland",
                        "IM" => "Isle of Man",
                        "IT" => "Italy",
                        "JE" => "Jersey",
                        "LV" => "Latvia",
                        "LI" => "Liechtenstein",
                        "LT" => "Lithuania",
                        "LU" => "Luxembourg",
                        "MK" => "Macedonia",
                        "MT" => "Malta",
                        "FX" => "Metropolitan France",
                        "MD" => "Moldova",
                        "MC" => "Monaco",
                        "ME" => "Montenegro",
                        "NL" => "Netherlands",
                        "NO" => "Norway",
                        "PL" => "Poland",
                        "PT" => "Portugal",
                        "RO" => "Romania",
                        "RU" => "Russia",
                        "SM" => "San Marino",
                        "RS" => "Serbia",
                        "CS" => "Serbia and Montenegro",
                        "SK" => "Slovakia",
                        "SI" => "Slovenia",
                        "ES" => "Spain",
                        "SJ" => "Svalbard and Jan Mayen",
                        "SE" => "Sweden",
                        "CH" => "Switzerland",
                        "UA" => "Ukraine",
                        "SU" => "Union of Soviet Socialist Republics",
                        "GB" => "United Kingdom",
                        "VA" => "Vatican City",
                        "AX" => "Åland Islands",
                    ],
                    "Oceania" => 
                    [
                        "AS" => "American Samoa",
                        "AQ" => "Antarctica",
                        "AU" => "Australia",
                        "BV" => "Bouvet Island",
                        "IO" => "British Indian Ocean Territory",
                        "CX" => "Christmas Island",
                        "CC" => "Cocos [Keeling] Islands",
                        "CK" => "Cook Islands",
                        "FJ" => "Fiji",
                        "PF" => "French Polynesia",
                        "TF" => "French Southern Territories",
                        "GU" => "Guam",
                        "HM" => "Heard Island and McDonald Islands",
                        "KI" => "Kiribati",
                        "MH" => "Marshall Islands",
                        "FM" => "Micronesia",
                        "NR" => "Nauru",
                        "NC" => "New Caledonia",
                        "NZ" => "New Zealand",
                        "NU" => "Niue",
                        "NF" => "Norfolk Island",
                        "MP" => "Northern Mariana Islands",
                        "PW" => "Palau",
                        "PG" => "Papua New Guinea",
                        "PN" => "Pitcairn Islands",
                        "WS" => "Samoa",
                        "SB" => "Solomon Islands",
                        "GS" => "South Georgia and the South Sandwich Islands",
                        "TK" => "Tokelau",
                        "TO" => "Tonga",
                        "TV" => "Tuvalu",
                        "UM" => "U.S. Minor Outlying Islands",
                        "VU" => "Vanuatu",
                        "WF" => "Wallis and Futuna",
                    ],
    ];     
}