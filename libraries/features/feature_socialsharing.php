<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_socialsharing extends ViewObject
{
    public $title           = 'Social Sharing';
    public $driver          = 'feature_socialsharing';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Add social sharing links to product pages.';
    public $system_type     = 'feature'; 

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');
    }

    public function install($installer=NULL)
    {
        $tables_installed = $installer->install_tables( $this->module_tables );
        return true;
    }

    public function uninstall($installer=NULL)
    {
        foreach($this->module_tables as $table_name => $table_data)
        {
            $this->dbforge->drop_table($table_name);
        }
        return true;
    }


    /**
     * Gets called/fired when any page is loaded
     * Alls the system to add assets
     */
    public function event_common($args=[]) 
    {
        $this->template->append_js('nitrocart::public/social.js');  
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