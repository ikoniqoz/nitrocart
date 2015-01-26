<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_product_comments extends ViewObject
{
    public $title           = 'Product Comments';
    public $driver          = 'feature_product_comments';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Allow your customers to write comments and discuss a product';
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
        $installer->dbforge->drop_table('nct_products_comments');
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
                              'name'=> 'Product Comments', 
                              'namespace'=>'nitrocart',
                              'path'=> 'features', 
                              'driver'=> 'feature_product_comments',
                              'prod_tab_order'=> 0, 
                              'routes'=>
                                    [
                                        [
                                            'name'  => 'Product Comments',
                                            'uri'   => '/comments(/:any)?',
                                            'dest'  => 'nitrocart/features/comments$1'
                                        ],
                                    ]
                                ];

    private $module_tables  =   [
            'nct_products_comments'     => 
            [
                'id'            => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' =>true, 'key' => true],
                'product_id'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'key' => true],
                'user_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'key' => true],
                'flagged'       => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>0],
                'visible'       => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>1],
                'comment'       => ['type' => 'TEXT'],
                'reffered'      => ['type' => 'TEXT'],
                'date_comment'  => ['type' => 'TIMESTAMP'],
            ],
        ];
}