<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Feature_vouchers extends ViewObject
{
    public $title           = 'Vouchers';
    public $driver          = 'feature_vouchers';
    public $require         = 'system_z_admin_layer';
    public $description     = 'TBA';
    public $system_type     = 'feature_x'; 
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');  
    }

    public function install($installer=NULL)
    {
        if($installer->install_tables($this->module_tables))
        {
            return true;
        }
        return false;
    }

    public function uninstall($installer=NULL)
    {
        $installer->dbforge->drop_table('nct_vouchers');
        $installer->dbforge->drop_table('nct_vouchers_uses');
        return true;
    }

    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }



    protected $module_tables = array(
        'nct_vouchers' => array(
            'id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'code'          => array('type' => 'VARCHAR', 'constraint' => '25', 'default'=>''),
            'user_id'       => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0),
            'credit'        => array('type' => 'INT', 'constraint' => '5', 'unsigned' => true, 'default'=>1),
            'balance'       => array('type' => 'INT', 'constraint' => '5', 'unsigned' => true, 'default'=>0),
            'created'       => array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
            'deleted'       => array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
        ),
        'nct_vouchers_uses'    => array(
            'id'                => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
            'voucher_id'        => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0),
            'order_id'          => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>0),
        ),        
    );

}