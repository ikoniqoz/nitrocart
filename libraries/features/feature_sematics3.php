<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_sematics3 extends ViewObject
{
    public $title           = 'Sematics3 Product Search';
    public $driver          = 'feature_sematics3';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Add quick import meta-info of your products with powerful sematics3 integration';
    public $system_type     = 'feature'; 

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');
    }

    public function install($installer=NULL)
    {
        $tables_installed = $installer->install_tables( $this->module_tables );
        $this->install_settings();
        $this->install_admin_menu();
        return true;
    }

    public function uninstall($installer=NULL)
    {
        foreach($this->module_tables as $table_name => $table_data)
        {
            $this->dbforge->drop_table($table_name);
        }
        $this->db->where('slug','ncs_s3_api_key')->delete('settings'); 
        $this->db->where('slug','ncs_s3_api_email_address')->delete('settings'); 
        $this->db->where('module','feature_sematics3')->delete('nct_admin_menu'); 
        return true;
    }

    private function install_admin_menu()
    {
        $data[] = array(
                'label'         => 'Online Product Search',
                'uri'           => NC_ADMIN_ROUTE.'/product_s3',
                'menu'          => 'lang:nitrocart:admin:shop_admin',
                'module'        => 'feature_sematics3',
                'order'         => 60,
                );
        $this->db->insert_batch('nct_admin_menu', $data);
        return true;
    }

    private function install_settings()
    {
        $settings = [
            [
                'slug' => 'ncs_s3_api_key',
                'title' => 'Sematics3 API Key',
                'description' => 'Please enter your Sematics3 API Key',
                'type' => 'text',
                'default' => '',
                'value' => '',
                'options' => '',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 200
            ],  
            [
                'slug' => 'ncs_s3_api_email_address',
                'title' => 'Sematics3 Email',
                'description' => 'Please enter your Sematics3 Email Address',
                'type' => 'text',
                'default' => '',
                'value' => '',
                'options' => '',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 200
            ],                       
        ];   
        $this->db->insert_batch('settings', $settings);
    }

    /**
     * Gets called/fired when any page is loaded
     * Alls the system to add assets
     */
    public function event_common($args=[]) 
    {   
    }


    /**
     * Specific event just for this feature
     */
    public function event_main($args=[])
    {
    }


        
    /**
     * Upgrade data 
     */
    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }


    protected $module_tables = [];

}