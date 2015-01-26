<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_api extends ViewObject
{
    public $title           = 'API Access';
    public $driver          = 'feature_api';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Allow API access to DB';
    public $system_type     = 'feature'; 

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');
    }

    public function install($installer=NULL)
    {
        $tables_installed = $installer->install_tables( $this->module_tables );

        if( $tables_installed  )
        {
            $this->add_menu_data();
            return true;
        }
        return false;
    }

    public function uninstall($installer=NULL)
    {
        foreach($this->module_tables as $table_name => $table_data)
        {
            $this->dbforge->drop_table($table_name);
        }
        $this->db->where('module', 'api')->delete('nct_admin_menu');    
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

    public function event_common($args=[]) {}

   
    private function add_menu_data()
    {
        $data = [];
        $data[] = array(
            'label'         => 'API Keys',
            'uri'           => NC_ADMIN_ROUTE.'/apis',
            'menu'          => 'lang:nitrocart:admin:shop_admin',
            'module'        => 'api',
            'order'         => 80,
            );

        $this->db->insert_batch('nct_admin_menu', $data);
    }
    protected $module_tables = array(
            'nct_api_keys' => 
            [
                //id of the api key
                'id'                => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true],
                
                //name of the key, could be a persons name or developers name
                'name'              => ['type' => 'VARCHAR', 'constraint' => '100'],

                //the api key itself
                'key'               => ['type' => 'VARCHAR', 'constraint' => '255'],

                //Total reqeust by user
                'tot_requests'      => ['type' => 'INT', 'constraint' => '15', 'unsigned' => true, 'null' => true, 'default' => 0],

                //total request this month by user
                'tot_curr_requests' => ['type' => 'INT', 'constraint' => '15', 'unsigned' => true, 'null' => true, 'default' => 0],

                //max allowed monthly usage
                'max_allowed'       => ['type' => 'INT', 'constraint' => '15', 'unsigned' => true, 'null' => true, 'default' => 0],

                //allow to access extendion
                'ax_extensions'     => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'null' => true, 'default' => 1],

                //allow to access products
                'ax_products'       => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'null' => true, 'default' => 1],

                //is the key enabled
                'enabled'       => ['type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'null' => true, 'default' => 1],
                
            ],
            'nct_api_requests' => 
            [
                'id'                => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true],
                'key_id'            => ['type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null'=>true],
                'endpoint'          => ['type' => 'VARCHAR', 'constraint' => '512'],
                'date'              => ['type' => 'DATETIME', 'null' => true, 'default' => NULL],               
                'result'            => ['type' => 'TEXT', 'null' => true, 'default' => NULL],               
            ],            
    );   

}