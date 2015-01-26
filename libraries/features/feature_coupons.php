<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_coupons extends ViewObject
{
    public $title           = 'Coupons';
    public $driver          = 'feature_coupons';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Create coupons for your store';
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
            $this->add_menu_data();
            return true;
        }
        return false;
    }

    public function uninstall($installer=NULL)
    {
        $installer->dbforge->drop_table('nct_coupons');
        $installer->dbforge->drop_table('nct_coupons_uses');
        $this->db->where('module', 'feature_coupons')->delete('nct_admin_menu');
        return true;
    }

    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }
    
    public function event_common($args=[]) {}


    private function add_menu_data()
    {
        $data = [];
        $data[] = [
            'label'         => 'lang:nitrocart:admin:coupons',
            'uri'           => NC_ADMIN_ROUTE.'/coupons',
            'menu'          => 'lang:nitrocart:admin:shop_admin',
            'module'        => 'feature_coupons',
            'order'         => 80,
        ];

        $this->db->insert_batch('nct_admin_menu', $data);
    }
    
    protected $module_tables = [
        'nct_coupons' => [
            'id'            => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true],
            'code'          => ['type' => 'VARCHAR', 'constraint' => '25', 'default'=>''],
            'max_use'       => ['type' => 'INT', 'constraint' => '5', 'unsigned' => true, 'default'=>1],
            'used_count'    => ['type' => 'INT', 'constraint' => '5', 'unsigned' => true, 'default'=>0],
            'pcent'         => ['type' => 'INT', 'constraint' => '5', 'unsigned' => true, 'default'=>5],
            'product_id'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0],
            'enabled'       => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>0],
            'created'       => ['type' => 'DATETIME', 'null' => true, 'default' => NULL],
            'updated'       => ['type' => 'DATETIME', 'null' => true, 'default' => NULL],
            'deleted'       => ['type' => 'DATETIME', 'null' => true, 'default' => NULL],
        ],
        'nct_coupons_uses' => [
            'id'            => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true],
            'code_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0],
            'user_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0],
            'order_id'      => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0],
        ],        
    ];

}