<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Module_Nitrocart extends Module
{
    


    /**
     * Release version
     */
    public $version         = '2.3.0';




    /**
     * Load core libraries and classes : Global
     */
    public function __construct()
    {
        $this->load->library('nitrocart/nitrocore_library');        
        $this->lang->load('nitrocart/nitrocart_admin');
        $this->ci = get_instance();
    }



    /**
     * [info description]
     * @return [type] [description]
     */
    public function info()
    {
        $and_get_menu = $this->ci->uri->segment(3);
        $info =  [
            'name' => [
                'en' => 'NitroCart',
            ],
            'description' => [              
                'en' => 'NitroCart  - A full featured ecommerce solution for NitroCMS',
            ],
            'skip_xss' => false,
            'frontend' => true,
            'backend' => true,
            'menu' => false,
            'author' => 'Salvatore Bordonaro',
            'roles' => [
                'admin_r_catalogue_view',
                'admin_r_catalogue_edit',
                'admin_customers',
                'admin_store_subsystems',
                'admin_reports',
                'admin_orders',
                'admin_checkout',
                'admin_packages',
                'admin_manage',
                'admin_workflows',
                'admin_tax',
                //new roles for nitrocart
                'admin_rbo_power_user',
                'admin_rbo_store_manager',
                'admin_rbo_catalogue',
                'admin_rbo_accounts',
                'admin_rbo_logistics',
                //features
                'admin_affiliates',
            ],
            'enabled' => 1,
            'is_core' => 0,
            'sections' => []
        ];


        /* 
         * We want to show the common 3 sections for all areas of 
         * NitroCart but not the features or subsystems
         */
        if( ! in_array( $and_get_menu , [ 'subsystems', 'features'] )) {
            $info['sections']['dashboard'] = [
                'name' => 'nitrocart:admin:dashboard',
                'uri' => NC_ADMIN_ROUTE.'/dashboard',
                'shortcuts' => []
            ];

            if(Settings::get('shop_store_type')=='standard')  {
                $info['sections']['orders'] = [
                    'name' => 'nitrocart:admin:orders',
                    'uri' => NC_ADMIN_ROUTE.'/orders',
                    'shortcuts' => []
                ];
            }

            $info['sections']['products'] = [
                'name' => 'nitrocart:admin:products',
                'uri' => NC_ADMIN_ROUTE.'/products',
                'shortcuts' => [ 
                    ['name' => 'nitrocart:products:create', 'uri' => NC_ADMIN_ROUTE.'/product/create','class' => 'add' ],
                ],
            ];
        }


        if(in_array( $and_get_menu , [ 'package_manager', 'packages', 'packages_groups'] ))
        {
            $info['sections']['packages'] = [
                'name' => 'nitrocart:admin:packages',
                'uri' => NC_ADMIN_ROUTE.'/packages',
                'shortcuts' => [ 
                        ['name' => 'nitrocart:packages:create', 'uri' => NC_ADMIN_ROUTE.'/packages/create','class' => 'add'],
                        ['name' => 'nitrocart:packages:create_group', 'uri' => NC_ADMIN_ROUTE.'/packages_groups/create','class' => 'add']
                ],                
            ];

            $info['sections']['packages_groups'] = [
                'name' => 'nitrocart:admin:packages_groups',
                'uri' => NC_ADMIN_ROUTE.'/packages_groups',
                'shortcuts' => [ 
                        ['name' => 'nitrocart:packages:create', 'uri' => NC_ADMIN_ROUTE.'/packages/create','class' => 'add'],
                        ['name' => 'nitrocart:packages:create_group', 'uri' => NC_ADMIN_ROUTE.'/packages_groups/create','class' => 'add'], 
                    ],  
            ];
        }

        /**
         * Common create menu item
         */
        if(in_array( $and_get_menu , ['coupons','workflows','tax'] ))
        {
            $info['sections'][$and_get_menu] = [
                'name' => 'nitrocart:admin:'.$and_get_menu,
                'uri' => NC_ADMIN_ROUTE.'/'.$and_get_menu,
                'shortcuts' => [
                        ['name' => 'nitrocart:admin:'.$and_get_menu.':create', 'uri' => NC_ADMIN_ROUTE.'/'.$and_get_menu.'/create','class' => 'add'] 
                    ]
            ];
        }


        /**
         * Common standard access
         */
        if(in_array( $and_get_menu , ['apis','carts', 'manage','shipping','gateways','customers','reports','sandbox'] ))
        {
            $info['sections'][$and_get_menu] = [
                'name' => 'nitrocart:admin:'.$and_get_menu,
                'uri' => NC_ADMIN_ROUTE.'/'.$and_get_menu,
                'shortcuts' => []
            ];
        }



        if(in_array( $and_get_menu , ['countries', 'shipping_zones','states'] ))
        {

            $info['sections']['countries'] = [
                'name' => 'Countries',
                'uri' => NC_ADMIN_ROUTE.'/countries',
                'shortcuts' => [ 
                                    ['name' => 'nitrocart:shipping:zones:create_country', 'uri' => NC_ADMIN_ROUTE.'/countries/create/' ,'class'=>'add'],
                               ]
            ];              
            $info['sections']['states'] = [
                'name' => 'States',
                'uri' => NC_ADMIN_ROUTE.'/states',
                'shortcuts' => [],
            ];   

            $info['sections']['states']['shortcuts'][0] =  ['name' => 'nitrocart:shipping:zones:create_state', 'uri' => NC_ADMIN_ROUTE.'/states/create/' ,'class'=>'add'];
            //tricky stuff
            if($this->uri->segment(3) == 'states' AND (($this->uri->segment(4) == 'bycountry') OR ($this->uri->segment(4) == 'create')) AND  $this->uri->segment(5) > 0)
            {
                $info['sections']['states']['shortcuts'][0]['uri'] =  NC_ADMIN_ROUTE.'/states/create/'.$this->uri->segment(5);     
            }
            $info['sections']['shipping_zones'] = [
                'name' => 'Zones',
                'uri' => NC_ADMIN_ROUTE.'/shipping_zones',
                'shortcuts' => [
                        ['name' => 'nitrocart:shipping:zones:create', 'uri' => NC_ADMIN_ROUTE.'/shipping_zones/create','class' => 'add'],
                ]  
            ];             
        }


        return $info;
    }


    /**
     * The main menu is now handled via a DB IO.
     * This function will peek into the DB and build the menu
     * TODO: Implement caching here
     */
    public function admin_menu(&$menu)
    {
        $this->ci->load->helper('nitrocart/nitrocart_admin');
        $display_menu       = true;

        $dev_menu = 'lang:nitrocart:admin:nitrocart_admin'; 

        if(group_has_role('nitrocart', 'admin_store_subsystems') )        
        {
            $menu['lang:cp:nav_addons']['lang:nitrocart:admin:store_subsystems']      = NC_ADMIN_ROUTE.'/subsystems';            
            $menu['lang:cp:nav_addons']['lang:nitrocart:admin:store_features']      = NC_ADMIN_ROUTE.'/features';
        }


        // if the table exist we can select all records from it!
        if( $this->db->table_exists('nct_admin_menu') )
        {            
            $menu_items = $this->db->order_by('order')->get('nct_admin_menu')->result();

            foreach ($menu_items as $key => $value)
            {
                $menu[$value->menu][$value->label] = $value->uri;
            }
        }

        // Place menu on position #
        add_admin_menu_place('lang:nitrocart:admin:shop', 1);

        add_admin_menu_place('lang:nitrocart:admin:shop_admin', 2);
    }


    /**
     * Installs nitrocart
     * @return [type] [description]
     */
    public function install()
    {
        $this->load->library('nitrocart/install_library'); 
        return $this->install_library->install_nitrocart( $this->uri->segment(6) );  
    }


    /**
     * Before uninstalling, check that all extension modules and features are uninstalled first
     */
    public function uninstall()
    {
        $this->load->library('nitrocart/install_library'); 
        return $this->install_library->uninstall_nitrocart();  
    }
    public function disable() { }
    
    public function enable() { }

    /**
     * Uprades all pending subsystems.
     */ 
    public function upgrade($old_version)
    {
        $this->load->library('nitrocart/install_library');   
        return $this->install_library->upgrade( $old_version );
    }


    public function help()
    {
        return "Please visit http://nitrocart.net";
    }


}
/* End of file details.php */