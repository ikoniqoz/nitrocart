<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_affiliates extends ViewObject
{
    public $title           = 'Affiliates';
    public $driver          = 'feature_affiliates';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Give Afiliates dedicated tracking links';
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
            Events::trigger("SHOPEVT_RegisterModule", $this->mod_details);
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
        $this->remove_menu_data();
        Events::trigger("SHOPEVT_DeRegisterModule", $this->mod_details);
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


    /**
     * Gets called/fired when any page is loaded
     * Alls the system to add assets
     */
    public function event_common($args=[]) 
    {
        if($code = $this->input->get('QUANTAM'))
        {
            $this->load->model('nitrocart/features/affiliates_m');
            $this->load->model('nitrocart/features/affiliates_clicks_m');

            //now we can do something
            if($affiliate = $this->affiliates_m->get_by('code', $code))
            {
                //now check for an existing row
                if( ! $this->affiliates_clicks_m->exist($affiliate->id, current_url()) )
                {
                    $this->affiliates_clicks_m->log($affiliate->id, current_url() );

                    //set in session
                    $this->session->set_userdata('QUANTAM', $code );

                }

            }
        } 
    }

    /**
     * Specific event just for this feature
     */
    public function event_main($args=[])
    {

    }


    private function add_menu_data()
    {
        $data = [];
        $data[] = array(
            'label'         => 'Affiliates',
            'uri'           => NC_ADMIN_ROUTE.'/affiliates',
            'menu'          => 'lang:nitrocart:admin:shop_admin',
            'module'        => 'feature_affiliates',
            'order'         => 80,
            );

        $this->db->insert_batch('nct_admin_menu', $data);
    }

    private function remove_menu_data()
    {
        $this->db->where('module','feature_affiliates')->delete('nct_admin_menu');    
    }  


    //this module/extention requires MY/Customer
    protected $mod_details = [
                          'name'=> 'Affiliates', 
                          'namespace'=>'feature_affiliates',
                          'path'=> 'features', 
                          'driver'=> 'feature_affiliates',
                          'prod_tab_order'=> 0, 
                          'routes'=>
                                [
                                    [
                                        'name'  => 'Affiliates',
                                        'uri'   => '/affiliates(/:any)?',
                                        'dest'  => 'nitrocart/admin/affiliates$1'
                                    ],
                                ]
                            ];


    protected $module_tables = 
    [
        'nct_affiliates' => 
        [
            'id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'name'          => array('type' => 'VARCHAR', 'constraint' => '100','default'=>''),
            'email'         => array('type' => 'VARCHAR', 'constraint' => '255','default'=>''),
            'code'          => array('type' => 'VARCHAR', 'constraint' => '255','default'=>''),
            'af_group'      => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0), 
            'total_clicks'  => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0),
            'enabled'       => array('type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'default'=>1),
            'deleted'       => array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
        ],
        'nct_affiliates_clicks' => 
        [
            'id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'affiliate_id'  => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0),
            'page'          => array('type' => 'VARCHAR', 'constraint' => '500','default'=>''),
            'info'          => array('type' => 'VARCHAR', 'constraint' => '2000','default'=>''),
            'ip_address'    => array('type' => 'VARCHAR', 'constraint' => '100','default'=>''),
            'date'          => array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
        ],
    ];

}