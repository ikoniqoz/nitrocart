<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_customer extends ViewObject
{       
    public $title           = 'Customer Portal';
    public $driver          = 'feature_customer';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Allow your customers to view their online order transactions';
    public $system_type     = 'feature'; 

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');  
    }

    public function install($installer=NULL)
    {
        Events::trigger("SHOPEVT_RegisterModule", $this->mod_details);
        return true;
    }

    public function uninstall($installer=NULL)
    {
        Events::trigger("SHOPEVT_DeRegisterModule", $this->mod_details);
        return true;
    }

    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }
    
    public function event_common($args=[]) {}
    
    protected $mod_details = [
                          'name'=> 'My', 
                          'namespace'=>'nitrocart',
                          'path'=> 'features', 
                          'driver'=> 'feature_customer',
                          'prod_tab_order'=> 0, 
                          'routes'=>
                                [
                                    [
                                        'name'  => 'Customer Portal Dashboard',
                                        'uri'   => '/my',
                                        'dest'  => 'nitrocart/my/my_portal'
                                    ],
                                    [
                                        'name'  => 'Customer Address List',
                                        'uri'   => '/my/addresses(/:any)?',
                                        'dest'  => 'nitrocart/my/addresses$1'
                                    ],
                                    [
                                        'name'  => 'Customer Orders [List]',
                                        'uri'   => '/my/orders(/:any)?',
                                        'dest'  => 'nitrocart/my/orders$1'
                                    ],
                                ]
                            ];

    private $module_tables  = [];
}