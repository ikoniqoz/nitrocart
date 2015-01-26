<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_search extends ViewObject
{
    public $title           = 'Search';
    public $driver          = 'feature_search';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Search Feature allows third party modules to extend the basic search features';
    public $system_type     = 'feature_x'; 
    
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
   

    protected $module_tables = array(
        'nct_search' => array(
            'id'            =>  array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => false, 'primary' => true), 

            //can we filter
            'admin_filter'  =>  array('type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>0), 
            'front_filter'  =>  array('type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>0), 

            //location of the models [some searches are avialable both front and back]
            'admin_model'   =>  array('type' => 'VARCHAR', 'constraint' => '512', 'default' => 'nitrocart/admin/products_admin_filter_m'),      
            'front_model'   =>  array('type' => 'VARCHAR', 'constraint' => '512', 'default' => 'nitrocart/products_filter_m'),      

            //result uri
            'admin_uri'     =>  array('type' => 'VARCHAR', 'constraint' => '512', 'default' => '{{x:uri x="ADMIN"}}/admin/product/edit/{id}'),            
            'front_uri'     =>  array('type' => 'VARCHAR', 'constraint' => '512', 'default' => '{{x:uri}}/products/product/{id}'),

            //other data
            'search_terms'  =>  array('type' => 'TEXT'),
            'date_indexed'  =>  array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
        ),
    );    

}