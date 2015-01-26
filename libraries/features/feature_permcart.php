<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_permcart extends ViewObject
{   
    public $title           = 'Permanant Cart';
    public $driver          = 'feature_permcart';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Allow your customers to store their carts in the DB';
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
        $installer->dbforge->drop_table('nct_carts');
        $this->db->where('module', 'carts')->delete('nct_admin_menu');        
        //Events::trigger("SHOPEVT_DeRegisterModule", $this->get_mod_data());
        return true;
    }

    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }


    private function add_menu_data()
    {
        $data = [];
        $data[] = array(
            'label'         => 'lang:nitrocart:admin:carts',
            'uri'           => NC_ADMIN_ROUTE.'/carts',
            'menu'          => 'lang:nitrocart:admin:shop_admin',
            'module'        => 'carts',
            'order'         => 80,
            );

        $this->db->insert_batch('nct_admin_menu', $data);
    }

    private function get_mod_data()
    {
        return 
        [
              'name'=> 'PermCarts', //Label of the module
              'namespace'=>'shop',
              'product-tab'=> false, //This is to tell the core that we want a tab
              'prod_tab_order'=> 0, //This is to tell the core that we want a tab
              'cart'=> false,
              'has_admin'=> false,
              'routes'=> [],
        ];
    }

    public function event_common()
    {
        
    }

    protected $module_tables = 
    [
        'nct_carts' => 
        [
            'id'            => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true],
            'user_id'       => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'key' => true],
            'variance_id'   => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0],
            'product_id'    => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0],
            'qty'           => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0],
            'price'         => ['type' => 'DECIMAL(10,2)', 'default' => 0], 
            'options'       => ['type' => 'TEXT','null'=>true], 
            'date'          => ['type' => 'DATETIME', 'null' => true, 'default' => NULL],
            'session'       => ['type' => 'TEXT','null'=>true], 
        ],
    ];
}