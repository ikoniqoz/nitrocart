<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_notifications extends ViewObject
{
    public $title           = 'Notification Center';
    public $driver          = 'feature_notifications';    
    public $require         = 'system_z_admin_layer';
    public $description     = 'Display a notification text on all store pages.';
    public $system_type     = 'feature'; 

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');        
    }

    public function install($installer=NULL)
    {
        $this->uninstall($installer);
        $settings = 
        [
            [
                'slug' => 'shop_notification',
                'title' => 'Enable Store Notifications',
                'description' => 'Would you like to enable the store notification',
                'type' => 'select',
                'default' => '0',
                'value' => '0',
                'options' => '0=No Thanks|1=Yes Please',
                'is_required' => true,
                'is_gui' => false,
                'module' => 'nitrocart',
                'order' => 200
            ],
            [
                'slug' => 'shop_notification_text',
                'title' => 'Notification',
                'description' => 'Enter the notification text to display.',
                'type' => 'textarea',
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
        return true;
    }

    public function uninstall($installer=NULL)
    {
        $this->db->where('slug','shop_notification')->delete('settings');
        $this->db->where('slug','shop_notification_text')->delete('settings');        
        return true;
    }

    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }


    public function event_common($args=[]) 
    {
        if($x = Settings::get('shop_notification'))
        {
            $this->session->set_flashdata('success',Settings::get('shop_notification_text'));
        }
    }



    private function add_menu_data()
    {
    }
    
    protected $module_tables = [];

}