<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

/**
 * Manages the install/uninstall of System_b
 *
 * @author NitroCMS Dev Team
 * @author NitroCart Dev Team 
 * @author Salvatore Bordonaro
 *
 */
class System_b_zones extends ViewObject
{
    protected $force_uninstall;

    public function __construct()
    {
        $this->load->driver('Streams');
        $this->force_uninstall = false;
    }

    public function install($installer=NULL)
    {

        if($installer->is_installed('System_b_zones')) return true;


        $this->uninstall($installer);

    
        /*Part A*/
        if ( $stream_zones_id  = $this->streams->streams->add_stream( 'Store: Zones',   'zones' , 'nc_zones' , 'nct_' ))
        {
            if ( $stream_zones_c_id  = $this->streams->streams->add_stream('Store: Zoned Countries', 'zones_countries',  'nc_zones' , 'nct_' ,  '')) 
            {
                if ( $stream_countries_id  = $this->streams->streams->add_stream('Store: Countries', 'countries',  'nc_zones' , 'nct_' ,  '')) 
                {
                    if ( $stream_customers_id  = $this->streams->streams->add_stream('Store: Customers', 'customers',  'nc_zones' , 'nct_' ,  '')) 
                    {
                        if ( $stream_addresses_id  = $this->streams->streams->add_stream('Store: Addresses', 'addresses',  'nc_zones' , 'nct_' ,  '')) 
                        {
                            if ( $tax_stream_id  = $this->streams->streams->add_stream('Store: TAX', 'tax',  'nc_zones' , 'nct_' ,  '')) 
                            {
                                if ( $packg_stream_id  = $this->streams->streams->add_stream('Store: Package Groups', 'packages_groups',  'nc_zones' , 'nct_' ,  '')) 
                                {
                                    if ( $pack_stream_id  = $this->streams->streams->add_stream('Store: Packages', 'packages',  'nc_zones' , 'nct_' ,  '')) 
                                    {

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
     

 
        //keep  track of where the fail is
        $passing = 20;

        if($stream_zones_id==false) $passing =  21;
        if($stream_zones_c_id==false) $passing =  22;
        if($stream_countries_id==false) $passing =  23;
        if($stream_customers_id==false) $passing =  24;
        if($stream_addresses_id==false) $passing =  25;
        if($tax_stream_id==false)   $passing =  26;
        if($packg_stream_id==false) $passing =  27;
        if($pack_stream_id==false)  $passing =  28;

        //if failing
        if( ! $passing ==  20)
        {
            $this->uninstall($installer);            
            $this->session->set_flashdata(JSONStatus::Error,'Failed at :'.$passing);
            redirect('admin/addons');
            return false;
        }
  
        //record in session
        $this->session->set_userdata('stream_zones_id',$stream_zones_id);
        $this->session->set_userdata('stream_zones_c_id',$stream_zones_c_id);
        $this->session->set_userdata('stream_customers_id',$stream_customers_id);
        $this->session->set_userdata('stream_countries_id',$stream_countries_id);
        $this->session->set_userdata('stream_addresses_id',$stream_addresses_id);        
        $this->session->set_userdata('tax_stream_id',$tax_stream_id);
        $this->session->set_userdata('packg_stream_id',$packg_stream_id);
        $this->session->set_userdata('pack_stream_id',$pack_stream_id);      

        $ref_fields = 
        [
            [
                'name'          => 'Deleted',
                'slug'          => 'deleted',
                'namespace'     => 'nc_zones',
                'type'          => 'datetime',
                'extra' => ['storage' => 'datetime'],
                'title_column'  => false,
                'required'      => false,
                'unique'        => false,
                'locked'        => true
            ],          
            [
                'name'          => 'First Name',
                'slug'          => 'first_name',
                'namespace'     => 'nc_zones',
                'type'          => 'text',
                'extra'         => ['max_length' => 100],
                'title_column'  => true,
                'required'      => true,
                'unique'        => false,
                'locked'        => true
            ],  
            [
                'name'          => 'Last Name',
                'slug'          => 'last_name',
                'namespace'     => 'nc_zones',
                'type'          => 'text',
                'extra'         => ['max_length' => 100],
                'title_column'  => false,
                'required'      => false,
                'unique'        => false,
                'locked'        => true
            ],                      
            [
                'name'          => 'Name',
                'slug'          => 'name',
                'namespace'     => 'nc_zones',
                'type'          => 'text',
                'extra'         => ['max_length' => 100],
                'title_column'  => true,
                'required'      => true,
                'unique'        => false,
                'locked'        => true
            ],    
            [
                'name'          => 'User ID',
                'slug'          => 'user_id',
                'namespace'     => 'nc_zones',
                'type'          => 'integer',
                'extra'         => ['max_length' => 11],
                'title_column'  => false,
                'required'      => true,
                'unique'        => false,
                'locked'        => true
            ],  
            [
                'name'          => 'Core',
                'slug'          => 'core',
                'namespace'     => 'nc_zones',
                'type'          => 'boolean',
                'extra'         => ['max_length' => 1,'dv'=>0,'false_text'=>'No','true_text'=>'Yes'],
                'title_column'  => false,
                'required'      => false,
                'unique'        => false,
                'locked'        => true
            ],  
            [
                'name'          => 'Default',
                'slug'          => 'default',
                'namespace'     => 'nc_zones',
                'type'          => 'boolean',
                'extra'         => ['max_length' => 1,'dv'=>1,'false_text'=>'No','true_text'=>'Yes'],
                'title_column'  => false,
                'required'      => false,
                'unique'        => false,
                'locked'        => true
            ],                                                                
        ];

        $this->streams->fields->add_fields($ref_fields);


        $this->streams->fields->assign_field( 'nc_zones', 'zones' , 'name', []);
        $this->streams->fields->assign_field( 'nc_zones', 'countries' , 'name', []);
        $this->streams->fields->assign_field( 'nc_zones', 'tax' , 'name', ['title_column'=>true]);
        $this->streams->fields->assign_field( 'nc_zones', 'packages' , 'name', []);
        $this->streams->fields->assign_field( 'nc_zones', 'packages_groups' , 'name', []);        

        $this->streams->fields->assign_field( 'nc_zones', 'customers' , 'user_id', []);  
        $this->streams->fields->assign_field( 'nc_zones', 'addresses' , 'user_id', []);  

        $this->streams->fields->assign_field( 'nc_zones', 'customers' , 'first_name', []);   
        $this->streams->fields->assign_field( 'nc_zones', 'addresses' , 'first_name', []);   

        $this->streams->fields->assign_field( 'nc_zones', 'customers' , 'last_name', []);   
        $this->streams->fields->assign_field( 'nc_zones', 'addresses' , 'last_name', []);   

        $this->streams->fields->assign_field( 'nc_zones', 'addresses' , 'deleted', []);   
        $this->streams->fields->assign_field( 'nc_zones', 'tax' , 'deleted', []);  
        $this->streams->fields->assign_field( 'nc_zones', 'packages' , 'deleted', []);  
        $this->streams->fields->assign_field( 'nc_zones', 'packages_groups' , 'deleted', []);  

        $this->streams->fields->assign_field( 'nc_zones', 'packages' , 'core', []);  
        $this->streams->fields->assign_field( 'nc_zones', 'packages_groups' , 'core', []);  

        $this->streams->fields->assign_field( 'nc_zones', 'tax' , 'default', []);  
        $this->streams->fields->assign_field( 'nc_zones', 'zones' , 'default', []);  
        $this->streams->fields->assign_field( 'nc_zones', 'packages' , 'default', []);  
        $this->streams->fields->assign_field( 'nc_zones', 'packages_groups' , 'default', []);  


        /*Part C*/
         $this->streams->fields->add_fields($this->streams_fields);

        return true;      
    }


    public function uninstall($installer=NULL)
    {
        if($this->force_uninstall)
        {
            $this->do_force_uninstall();
        }        
        $this->streams->utilities->remove_namespace('nc_zones');                          
        return true;
    }


    public function upgrade($installer,$old_version)
    {
        $this->load->library('nitrocart/Toolbox/Nc_status');
        $ncmo = new NCMessageObject();
        return $ncmo;
    }


    public function do_force_uninstall()
    {
        if($this->db->table_exists('nct_tax'))
        {
            $this->dbforge->drop_table('nct_tax'); 
        }    
        if($this->db->table_exists('nct_packages_groups'))
        {
            $this->dbforge->drop_table('nct_packages_groups'); 
        }  
        if($this->db->table_exists('nct_packages'))
        {
            $this->dbforge->drop_table('nct_packages'); 
        }                           
        return true;
    }  

    private $streams_fields = 
    [
            [ 'name' => 'Description'          , 'slug' => 'description'        , 'assign'    => 'zones'            , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 400)  , 'title_column' => true  , 'required' => true , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Zone ID'              , 'slug' => 'zone_id'            , 'assign'    => 'zones_countries'  , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)   , 'title_column' => true  , 'required' => true , 'unique' => false , 'locked' =>true ], 
            [ 'name' => 'Country ID'           , 'slug' => 'country_id'         , 'assign'    => 'zones_countries'  , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)   , 'title_column' => true  , 'required' => false , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Country'              , 'slug' => 'country_t'          , 'assign'    => 'zones_countries'  , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 50)   , 'title_column' => true  , 'required' => true , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'ISO 3166-1 (Alpha-2)' , 'slug' => 'code2'              , 'assign'    => 'countries'        , 'namespace' => 'nc_zones', 'type' => 'iso31661'       , 'extra' => array('max_length' => 2 , 'type'=>'alpha2' )       , 'title_column' => false  , 'required' => true , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'ISO 3166-1 (Alpha-3)' , 'slug' => 'code3'              , 'assign'    => 'countries'        , 'namespace' => 'nc_zones', 'type' => 'iso31661'       , 'extra' => array('max_length' => 3 , 'type'=>'alpha3')       , 'title_column' => false  , 'required' => true , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'ISO 3166-1 (Numeric)' , 'slug' => 'coden'              , 'assign'    => 'countries'        , 'namespace' => 'nc_zones', 'type' => 'iso31661'       , 'extra' => array('max_length' => 4 , 'type'=>'numeric')       , 'title_column' => false  , 'required' => true , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Region'               , 'slug' => 'region'             , 'assign'    => 'countries'        , 'namespace' => 'nc_zones', 'type' => 'global_regions' , 'extra' => array('max_length' => 255,'dv'=>'Europe','group'=>'uncg')                    , 'title_column' => false  , 'required' => false, 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Enabled'              , 'slug' => 'enabled'            , 'assign'    => 'countries'        , 'namespace' => 'nc_zones', 'type' => 'boolean'        , 'extra' => array('max_length' => 1,'dv'=>1,'false_text'=>'No','true_text'=>'Yes')                      , 'title_column' => false  , 'required' => false, 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Billing Address ID'   , 'slug' => 'default_billing_id' , 'assign'    => 'customers'        , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)      , 'title_column' => true  , 'required' => false  , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Shipping Address ID'  , 'slug' => 'default_shipping_id', 'assign'    => 'customers'        , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)      , 'title_column' => true  , 'required' => false  , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Signup Email'         , 'slug' => 'signup_email'       , 'assign'    => 'customers'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 150)     , 'title_column' => true  , 'required' => false , 'unique' => false , 'locked' =>true ],
            [ 'name' => 'Total Orders'         , 'slug' => 'total_orders'       , 'assign'    => 'customers'        , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)      , 'title_column' => true  , 'required' => false  , 'unique' => false , 'locked' =>true],
            [ 'name' => 'Last Order'           , 'slug' => 'last_order'         , 'assign'    => 'customers'        , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)      , 'title_column' => true  , 'required' => false  , 'unique' => false, 'locked' =>true],
            [ 'name' => 'Account ID'           , 'slug' => 'account_id'         , 'assign'    => 'customers'        , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)      , 'title_column' => true  , 'required' => false  , 'unique' => false ],
            [ 'name' => 'Store Credit'         , 'slug' => 'store_credit'       , 'assign'    => 'customers'        , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2)    , 'title_column' => true  , 'required' => false , 'unique' => false ],
            [ 'name' => 'Email'                , 'slug' => 'email'              , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 150)     , 'title_column' => false  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'Address 1'            , 'slug' => 'address1'           , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 100)     , 'title_column' => true  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'Address 2'            , 'slug' => 'address2'           , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 100)     , 'title_column' => false  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'City'                 , 'slug' => 'city'               , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 80)      , 'title_column' => false  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'State'                , 'slug' => 'state'              , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 50)      , 'title_column' => false  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'Country'              , 'slug' => 'country'            , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'iso31661'       , 'extra' => array('type'=>'alpha2', 'dynamic'=>true)  , 'title_column' => false  , 'required' => false , 'unique' => false , 'locked'=>true] ,
            [ 'name' => 'Zip'                  , 'slug' => 'zip'                , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 10)      , 'title_column' => false  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'Phone'                , 'slug' => 'phone'              , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 20)      , 'title_column' => false  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'Billing'              , 'slug' => 'billing'            , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'boolean'        , 'extra' => array('max_length' => 1,'dv'=>0,'false_text'=>'No','true_text'=>'Yes')        , 'title_column' => true  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'Shipping'             , 'slug' => 'shipping'           , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'boolean'        , 'extra' => array('max_length' => 1,'dv'=>0,'false_text'=>'No','true_text'=>'Yes')       , 'title_column' => true  , 'required' => false , 'unique' => false , 'locked'=>true],
            [ 'name' => 'Instruction'          , 'slug' => 'instruction'        , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 512)     , 'title_column' => false  , 'required' => false , 'unique' => false ],
            [ 'name' => 'Company'              , 'slug' => 'company'            , 'assign'    => 'addresses'        , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 100)     , 'title_column' => false  , 'required' => false , 'unique' => false ],
            [ 'name' => 'Rate'                 , 'slug' => 'rate'               , 'assign'    => 'tax'              , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2)    , 'title_column' => false  , 'required' => false  , 'unique' => false  , 'locked' =>true ],
            [ 'name' => 'PKG Group ID'         , 'slug' => 'pkg_group_id'       , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'integer'        , 'extra' => array('max_length' => 11)      , 'title_column' => true    , 'required' => true , 'unique' => false , 'locked' =>true],
            [ 'name' => 'Code'                 , 'slug' => 'code'               , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'text'           , 'extra' => array('max_length' => 100)     , 'title_column' => true    , 'required' => true , 'unique' => false , 'locked' =>true],
            [ 'name' => 'Height'               , 'slug' => 'height'             , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Width'                , 'slug' => 'width'              , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Length'               , 'slug' => 'length'             , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Weight'               , 'slug' => 'weight'             , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Outer Height'         , 'slug' => 'outer_height'       , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Outer Width'          , 'slug' => 'outer_width'        , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Outer Length'         , 'slug' => 'outer_length'       , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Max Weight'           , 'slug' => 'max_weight'         , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false , 'locked' =>true],
            [ 'name' => 'Curr Weight'          , 'slug' => 'cur_weight'         , 'assign'    => 'packages'         , 'namespace' => 'nc_zones', 'type' => 'decimal'        , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.01),  'title_column' => true  , 'required' => false, 'unique' => false, 'locked' =>true],
    ];

}