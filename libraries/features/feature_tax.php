<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_tax extends ViewObject
{ 
    public $title           = 'Tax';
    public $driver          = 'feature_tax';
    public $require         = 'system_z_admin_layer';
    public $description     = 'Tax System - Install this if you want to apply tax to your products';
    public $system_type     = 'feature'; 
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');      
    }


    public function install($installer=NULL)
    {
        //menu
        $data = array();
        $data[] = array(
            'label'         => 'lang:nitrocart:admin:tax',
            'uri'           => NC_ADMIN_ROUTE.'/tax',
            'menu'          => 'lang:nitrocart:admin:shop_admin',
            'module'        => 'tax',
            'order'         => 90,
            );
        $this->db->insert_batch('nct_admin_menu', $data);

        return true;
    }

    public function event_common()
    {
        
    }
    public function uninstall($installer=NULL)
    {
        //remove tax from the menu
        $this->db->where('module','tax')->delete('nct_admin_menu');

        //delete the data data
        //$this->db->truncate('nct_tax');

        //remove all tax records/assignments for the products
        //$this->db->update('nct_products',array('tax_id'=>NULL));

        return true;
    }


    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }
 

}