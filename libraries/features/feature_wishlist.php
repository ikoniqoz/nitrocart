<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_wishlist extends ViewObject
{   
    public $title           = 'Wishlist';
    public $driver          = 'feature_wishlist';
    public $require         = 'feature_customer';
    public $description     = 'Allow your customers to  have a wishlist';
    public $system_type     = 'feature'; 
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');  
    }


    public function install($installer=NULL)
    {
        if($installer->install_tables($this->module_tables))
        {
            Events::trigger("SHOPEVT_RegisterModule", $this->mod_details);
            return true;
        }
        return false;
    }

    public function uninstall($installer=NULL)
    {
        $installer->dbforge->drop_table('nct_wishlist');
        Events::trigger("SHOPEVT_DeRegisterModule", $this->mod_details);
        return true;
    }


    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }
    
    public function event_common($args=[]) {}

    //this module/extention requires MY/Customer
    protected $mod_details = [
                          'name'=> 'My Wishlist', 
                          'namespace'=>'nitrocart',
                          'path'=> 'features', 
                          'driver'=> 'feature_wishlist',
                          'prod_tab_order'=> 0, 
                          'routes'=>
                                [
                                    [
                                        'name'  => 'Customer Whishlist',
                                        'uri'   => '/my/wishlist(/:any)?',
                                        'dest'  => 'nitrocart/my/wishlist$1'
                                    ],
                                ]
                            ];



    private $module_tables  =   [
            'nct_wishlist'     => 
            [
                'user_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'key' => true],
                'product_id'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'key' => true],
                'price'         => ['type' => 'DECIMAL(10,2)', 'default' => 0], /*price at time of adding*/
                'user_notified' => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true],
                'date_added'    => ['type' => 'TIMESTAMP'],
            ],
        ];
}