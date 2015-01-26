<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_product_reviews extends ViewObject
{     
    public $title           = 'Product Reviews';
    public $driver          = 'feature_product_reviews';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Allow your customers to write reviews';
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
        $installer->dbforge->drop_table('nct_products_reviews');
        Events::trigger("SHOPEVT_DeRegisterModule", $this->mod_details);
        return true;
    }

    public function event_common()
    {
        
    }


    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }

    protected $mod_details = [
                              'name'=> 'Product Reviews', 
                              'namespace'=>'nitrocart',
                              'path'=> 'features', 
                              'driver'=> 'feature_product_reviews',
                              'prod_tab_order'=> 0, 
                              'routes'=>
                                    [
                                        [
                                            'name'  => 'Product Reviews',
                                            'uri'   => '/reviews(/:any)?',
                                            'dest'  => 'nitrocart/features/reviews$1'
                                        ],
                                    ]
                                ];

    private $module_tables  =   [
            'nct_products_reviews'     => 
            [
                'id'            => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' =>true, 'key' => true],
                'product_id'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'key' => true],
                'user_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'key' => true],
                'flagged'       => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>0],
                'visible'       => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>1],
                'rating'        => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>0],
                'comment'       => ['type' => 'TEXT'],
                'reffered'      => ['type' => 'TEXT'],
                'date_reviewed' => ['type' => 'TIMESTAMP'],
            ],
        ];
}