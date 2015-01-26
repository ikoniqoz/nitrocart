<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_workflows extends ViewObject
{ 
    public $title           = 'Workflow Management';
    public $driver          = 'feature_workflows';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Allow Admins to customize order workflows';
    public $system_type     = 'feature'; 
    
	public function __construct()
	{
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');        
	}


    public function install($installer=NULL)
    {
        $data[] = array(
                'label'         => 'Workflows',
                'uri'           => NC_ADMIN_ROUTE.'/workflows',
                'menu'          => 'lang:nitrocart:admin:shop_admin',
                'module'        => 'feature_workflows',
                'order'         => 60,
                );
        $this->db->insert_batch('nct_admin_menu', $data);
        return true;
    }

    public function event_common()
    {
        
    }
    
    /**
     * We do not remove the setup workflows. Just leave them in
     * @param  [type] $installer [description]
     * @return [type]            [description]
     */
    public function uninstall($installer=NULL)
    {
        $this->db->where('module','feature_workflows')->delete('nct_admin_menu');
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
}