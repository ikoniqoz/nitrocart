<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_reports extends ViewObject
{  
    public $title           = 'Reports';
    public $driver          = 'feature_reports';
    public $require         = 'system_z_admin_layer';
    public $description     = 'System/Product reports';
    public $system_type     = 'feature'; 
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');  
    }

    public function install($installer=NULL)
    {
        // Install tables
        $tables_installed = $installer->install_tables( $this->module_tables );

        // if the tables installed, now time to register this sub-module with
        if( $tables_installed  )
        {

            $this->add_menu_data();
            $this->populate_reports_data();
            return true;
        }

        return false;
    }

    public function event_common()
    {
        
    }
    
    private function populate_reports_data()
    {

        $data = [];
        $data[] = array(
            'name'          => 'Best Customers',
            'last_exported' => NULL, //date("Y-m-d H:i:s"),
            'module'        => 'nitrocart',
            'type'          => 'Clients',
            'slug'          => 'bestcustomers',
            'path'          => $this->config->item('core/path').'/admin/reports/allorders/bestcustomers',         
            'enabled'       => 1,
            );

        $data[] = array(
            'name'          => 'High orders',
            'last_exported' => NULL,
            'module'        => 'nitrocart',
            'type'          => 'Orders',
            'slug'          => 'bestcustomers',
            'path'          => $this->config->item('core/path').'/admin/reports/allorders/highorders',
            'enabled'       => 1,
            );

        $data[] = array(
            'name'          => 'Most Viewed',
            'last_exported' => NULL,
            'module'        => 'nitrocart',
            'type'          => 'Products',
            'slug'          => 'mostviewed',
            'path'          => $this->config->item('core/path').'/admin/reports/daterange/mostviewed',
            'enabled'       => 1,
            );   

        $data[] = array(
            'name'          => 'Best Sellers',
            'last_exported' => NULL,
            'module'        => 'nitrocart',
            'type'          => 'Products',
            'slug'          => 'mostsoldp',
            'path'          => $this->config->item('core/path').'/admin/reports/daterange/mostsoldp',
            'enabled'       => 1,
            );   


        $this->db->insert_batch('nct_reports', $data);
    }


    private function add_menu_data()
    {
        $data = [];
        $data[] = array(
            'label'         => 'lang:nitrocart:admin:reports',
            'uri'           => NC_ADMIN_ROUTE.'/reports',
            'menu'          => 'lang:nitrocart:admin:shop_admin',
            'module'        => 'reports',
            'order'         => 80,
            );

        $this->db->insert_batch('nct_admin_menu', $data);
    }


    public function uninstall($installer=NULL)
    {
        foreach($this->module_tables as $table_name => $table_data)
        {
            $this->dbforge->drop_table($table_name);
        }
        $this->db->where('module', 'reports')->delete('nct_admin_menu');
        return true;
    }

    /**
     * Upgrade data 
     */
    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }


 


    //List of tables used
    protected $module_tables = array(

        'nct_reports' => array(
            'id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'name'          => array('type' => 'VARCHAR', 'constraint' => '100','default'=>'Standard'),
            'type'          => array('type' => 'VARCHAR', 'constraint' => '100','default'=>'Products'),
            'ordering_count'=> array('type' => 'INT', 'constraint' => '4', 'unsigned' => true, 'default'=> 10  ),
            'description'  => array('type' => 'VARCHAR', 'constraint' => '200','default'=>''),
            'last_exported' => array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
            'module'        => array('type' => 'VARCHAR', 'constraint' => '100','default'=>'core'),
            'slug'          => array('type' => 'VARCHAR', 'constraint' => '100','default'=>'bydate'), //slug used for views
            'path'          => array('type' => 'VARCHAR', 'constraint' => '100','default'=>'nitrocart/reports/bydate'), //controller_api
            'enabled'       => array('type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=> 1  ),
        ),

    );


}