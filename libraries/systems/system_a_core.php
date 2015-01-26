<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class System_a_core extends Store_module 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');  
    }


    /**
     * Installs the Subsystem into NitroCart and database
     */
    public function install($installer=NULL)
    {
        //test
        if($installer->is_installed('System_a_core')) return true;

        $this->uninstall($installer);

        //Note: that currently shop uses a number of different formats. So careful when updating the rest
        $__datetime_field       = array('type' => 'DATETIME', 'null' => true, 'default' => NULL);
        //Common fields for SHOP
        $__pk_field             = array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true);
        $__fk_field             = array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true);
        $__boolean_0_field      = array('type' => 'INT', 'constraint' => '1' , 'unsigned' => true, 'null' => true, 'default' => 0);
        $__boolean_1_field      = array('type' => 'INT', 'constraint' => '1' , 'unsigned' => true, 'null' => true, 'default' => 1);      
        $__shop_id_field        = array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default' => 0);
        $__created_by_field     = array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default' => 0);
        $__std_integer_field    = array('type' => 'INT', 'constraint' => '5' , 'unsigned' => true, 'default' => 0);
        $__std_text_field       = array('type' => 'TEXT', 'null' => true, 'default' => NULL);
        $__hwdw_field           = array('type' => 'DECIMAL(6,2)', 'default' => 0);

        $__currency_field       = array('type' => 'DECIMAL(10,2)', 'unsigned' => true, 'null' => true, 'default' => 0);
        $__varchar_100_field    = array('type' => 'VARCHAR', 'constraint' => '100', 'default' => '');
        $__varchar_255_field    = array('type' => 'VARCHAR', 'constraint' => '255', 'default' => '');

        $tables = 
        [
            'nct_modules'         => 
            [
                'id'                    => $__pk_field,
                'name'                  => $__varchar_100_field,
                'namespace'             => $__varchar_100_field,
                'path'                  => $__varchar_100_field,
                'driver'                => $__varchar_100_field,
                'type'                  => $__varchar_100_field,
                'installed'             => $__boolean_0_field,
                'core'                  => $__boolean_0_field,
                'prod_tab_order'        => $__std_integer_field,
                'ordering_count'        => $__std_integer_field,
                'created'               => $__datetime_field,
                'updated'               => $__datetime_field,
                'created_by'            => $__fk_field,                   
            ],
            'nct_routes'         => 
            [
                'id'                    => $__pk_field,
                'name'                  => $__varchar_100_field,
                'module'                => $__varchar_100_field,
                'uri'                   => $__varchar_255_field,
                'default_uri'           => $__varchar_255_field,
                'dest'                  => $__varchar_255_field,
                'ordering_count'        => $__std_integer_field,
                'created'               => $__datetime_field,
                'updated'               => $__datetime_field,
                'created_by'            => $__fk_field,                
            ],
            'nct_checkout_options'         => 
            [
                'id'                    => $__pk_field,
                'title'                 => $__varchar_255_field,
                'slug'                  => $__varchar_255_field,                
                'description'           => $__std_text_field,                
                'options'               => $__std_text_field,
                'enabled'               => $__boolean_0_field,
                'module_type'           => $__varchar_100_field,
                'ordering_count'        => $__std_integer_field,
                'deleted'               => $__datetime_field,
                'created'               => $__datetime_field,
                'updated'               => $__datetime_field,
                'created_by'            => $__fk_field,
            ],
            'nct_transactions'         => 
            [
                'id'                    => $__pk_field,
                'order_id'              => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true),
                'txn_id'                => array('type' => 'VARCHAR', 'constraint' => '255'),
                'status'                => array('type' => 'VARCHAR', 'constraint' => '30', 'null' => true, 'default'=>''), //array('type' => "ENUM('pending','accepted','rejected')", 'default' => 'pending'),
                'reason'                => $__std_text_field,
                'amount'                => $__currency_field,   /*credit to shop*/
                'refund'                => $__currency_field,   /*debit from shop*/
                'gateway'               => $__varchar_100_field,
                'user'                  => array('type' => 'VARCHAR', 'constraint' => '50'), /*SYSTEM/ADMIN/CUSTOMER*/
                'data'                  => $__std_text_field,
                'timestamp'             => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true),
                'created_by'            => $__created_by_field,
                'created'               => $__datetime_field,
                'updated'               => $__datetime_field,
                'deleted'               => $__datetime_field,
            ],
            'nct_orders'               => 
            [
                'id'                    => $__pk_field,
                'user_id'               => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'status_id'             => array('type' => "INT", 'constraint' => '11', 'unsigned' => true, 'default' => 0),  //workflow id              
                'status'                => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => 'Pending',),
                'cust_status'           => array('type' => 'TEXT', 'null' => true, 'default' => NULL),
                'order_date'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'paid_date'             => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,'null' => true, 'default' => NULL),
                'total_tax'             => $__currency_field,
                'total_discount'        => $__currency_field,
                'total_subtotal'        => $__currency_field,
                'total_totals'          => $__currency_field,
                'total_shipping'        => $__currency_field,        
                //Once paid these points go to the users profile
                //Points are only available for members, the total will be assigned to their customer profile. 
                'total_points'          => array('type' => "INT", 'constraint' => '11', 'unsigned' => true, 'default' => 0), 
                'count_items'           => array('type' => "INT", 'constraint' => '5', 'unsigned' => true, 'default' => 0), //total # of items (sum of qty not line items)
                'shipping_id'           => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'gateway_id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'billing_address_id'    => array('type' => "INT", 'constraint' => '11', 'unsigned' => true, 'default' => 0),
                'shipping_address_id'   => array('type' => "INT", 'constraint' => '11', 'unsigned' => true, 'default' => 0, 'null'=>true),
                'has_shipping_address'  => array('type' => 'INT', 'constraint' => '1', 'unsigned' => true, 'null'=>true),
                'session_id'            => array('type' => 'VARCHAR', 'constraint' => '40', 'default' => '',),
                'ip_address'            => array('type' => 'VARCHAR', 'constraint' => '40', 'default' => '',),
                'data'                  => $__std_text_field,
                'delete_message'        => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => '',),
                'created_by'            => $__created_by_field,
                'created'               => $__datetime_field,
                'updated'               => $__datetime_field,
                'deleted'               => $__datetime_field,
            ],
            'nct_order_items' => 
            [
                'id'                    => $__pk_field,
                'order_id'              => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'product_id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'variant_id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'options'               => $__std_text_field,
                'title'                 => $__varchar_100_field,
                'qty'                   => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true),
            ],
            'nct_order_invoice' => 
            [
                'id'                    => $__pk_field,
                'order_id'              => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'title'                 => array('type' => 'VARCHAR', 'constraint' => '255', 'default' => '',),
                'product_id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null'=>true, 'default'=>NULL),
                'variant_id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null'=>true, 'default'=>NULL),
                'qty'                   => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'default'=>1),                
                'discount_message'      => $__std_text_field,
                'tax'                   => $__currency_field, 
                'price'                 => $__currency_field,     
                'base'                  => $__currency_field,                
                'discount'              => $__currency_field,                 
                'tax_rate'              => $__currency_field, 
                'subtotal'              => $__currency_field,
                'total'                 => $__currency_field, 
                'created_by'            => $__created_by_field,
                'created'               => $__datetime_field,
                'updated'               => $__datetime_field,
                'deleted'               => $__datetime_field,                
            ],
            'nct_order_notes'  => 
            [
                'id'                    => $__pk_field,
                'order_id'              => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'user_id'               => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'message'               => $__std_text_field,
                'date'                  => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true,),
                'created_by'            => $__created_by_field,
                'created'               => $__datetime_field,
                'updated'               => $__datetime_field,
                'deleted'               => $__datetime_field,
            ],
            'nct_workflows' => 
            [
                'id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
                'name'          => array('type' => 'VARCHAR', 'constraint' => '100'),
                'section'       => array('type' => 'VARCHAR', 'constraint' => '100', 'default'=>'orders'),
                'core'          => array('type' => 'INT', 'constraint' => '1', 'default'=>0),
                'is_placed'     => array('type' => 'INT', 'constraint' => '1', 'default'=>0),
                'pcent'         => array('type' => 'INT', 'constraint' => '4', 'default'=>0), //order
                'require'       => array('type' => 'INT', 'constraint' => '11', 'default'=>0), //zero for no require, otherwise if referenced before switching to must be at the required status workflow id
                'deleted'       => array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
            ],            
            'nct_attributes' => 
            [
                'id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
                'name'          => array('type' => 'VARCHAR', 'constraint' => '100'),
                'slug'          => array('type' => 'VARCHAR', 'constraint' => '100'),
                'deleted'       => array('type' => 'DATETIME', 'null' => true, 'default' => NULL),
            ],  
            'nct_e_attributes' => 
            [
                'id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
                'e_product'     => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true),
                'e_variance'    => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'e_label'       => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => ''),
                'e_value'       => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => ''),
                'e_type'        => array('type' => 'VARCHAR', 'constraint' => '100', 'default' => 'int'), /*string|int*/
                'e_data'        => array('type' => 'TEXT', 'null' => true, 'default' => NULL),
                'e_notes'       => array('type' => 'TEXT', 'null' => true, 'default' => NULL),
            ], 
            /*
             * pid = product.id
             * vid = variance.id
             * aid = attribute.id
             * eaid = e_attribute.id
             */
            'nct_e_int_values' => 
            [
                'id'                    => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
                'product_id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'variance_id'           => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'attribute_id'          => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'e_attribute_id'        => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'value'                 => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => false, 'default'=>0),
            ], 
            'nct_e_string_values' => 
            [
                'id'                    => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'auto_increment' => true, 'primary' => true),
                'product_id'            => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'variance_id'           => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'attribute_id'          => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'e_attribute_id'        => array('type' => 'INT', 'constraint' => '11', 'unsigned' => true, 'null' => true, 'default'=>NULL),
                'value'                 => array('type' => 'VARCHAR', 'constraint' => '250'),
            ],             
        ]; 


        $installer->install_tables( $tables );

        return true;
    }

    public function uninstall($installer=NULL)
    {
        $this->dbforge->drop_table('nct_modules');
        $this->dbforge->drop_table('nct_routes'); 
        $this->dbforge->drop_table('nct_checkout_options');        

        $this->dbforge->drop_table('nct_transactions');
        $this->dbforge->drop_table('nct_orders');
        $this->dbforge->drop_table('nct_order_items');
        $this->dbforge->drop_table('nct_order_notes');
        $this->dbforge->drop_table('nct_order_invoice');
        $this->dbforge->drop_table('nct_workflows');
        $this->dbforge->drop_table('nct_attributes');
        $this->dbforge->drop_table('nct_e_attributes'); 
        $this->dbforge->drop_table('nct_e_int_values');   
        $this->dbforge->drop_table('nct_e_string_values');

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