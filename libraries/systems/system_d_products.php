<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class System_d_products extends Store_module 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('nitrocart/Toolbox/Nc_status');  
        $this->config->load('nitrocart/install/' . NC_CONFIG);
    }

    public function install($installer=NULL)
    {

        if($installer->is_installed('System_d_products')) return true;

         //do not continue if cant execute an uninstall
        if( ! $this->uninstall($installer))
        {
            return false;
        }
        

        $prod_stream_id                 = $this->_createStreamTable( array('assign_to' => 'products'                , 'namespace' => 'nc_products'  , 'title' => 'Store: Products' , 'desc' => '', 'prefix' => 'nct_')); 
        $products_variances_stream_id   = $this->_createStreamTable( array('assign_to' => 'products_variances'      , 'namespace' => 'nc_product_variances' , 'title' => 'Store: Product Variances', 'desc' => '', 'prefix' => 'nct_') );
        $products_types_stream_id       = $this->_createStreamTable( array('assign_to' => 'products_types'          , 'namespace' => 'nc_products_types'     , 'title' => 'Store: Product Types', 'desc' => '', 'prefix' => 'nct_'));
        $stream_state_id                = $this->_createStreamTable( array('assign_to' => 'states'                  , 'namespace' => 'nc_states'  , 'title' => 'Store: States', 'desc' => '', 'prefix' => 'nct_') );        


        $passing = 40;
        if($prod_stream_id==false) $passing =  41;
        if($products_variances_stream_id==false) $passing =  42;
        if($products_types_stream_id==false) $passing =  43;


        //if failing
        if($passing !=  40)
        {
            $this->uninstall($installer);            
            $this->session->set_flashdata('error','Failed at :'.$passing);
            redirect('admin/addons');
            return false;
        }

        //get the tax id
        $tax_stream_id = $this->session->userdata('tax_stream_id');

        $tax_field_required = $this->config->item('install/tax_field_is_required');



        //get the id of the countries stream
        $countries_stream = $this->session->userdata('stream_countries_id');


        $streams_fields = 
        [

            //$product_fields = array(
            array( 'name' => 'Deleted'     , 'slug' => 'deleted'       , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'datetime'      , 'extra' => array('storage' => 'datetime')         , 'title_column' => false   , 'required' => false, 'unique' => false ,'locked'=>true ),
            array( 'name' => 'Name'        , 'slug' => 'name'          , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'text'          , 'extra' => array('max_length' => 250)             , 'title_column' => true    , 'required' => true , 'unique' => false ,'locked'=>true ),
            array( 'name' => 'Slug'        , 'slug' => 'slug'          , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'slug'          , 'extra' => array('space_type' => '-', 'slug_field'=>'name')     , 'title_column' => false    , 'required' => true , 'unique' => true  ,'locked'=>true ),
            array( 'name' => 'Views'       , 'slug' => 'views'         , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'integer'    , 'extra' => array('max_length' => 8,'readonly'=>true)               , 'title_column' => false    , 'required' => false, 'unique' => false, 'locked'=>true ),
            array( 'name' => 'Public'      , 'slug' => 'public'        , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'boolean'       , 'extra' => array('default_value' => 0, 'false_text'=>'Hidden','true_text'=>'Visible')       , 'title_column' => false    , 'required' => false, 'unique' => false, 'locked'=>true ),     
            array( 'name' => 'Featured'    , 'slug' => 'featured'      , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'boolean'       , 'extra' => array('default_value' => 1, 'false_text'=>'No','true_text'=>'Yes') , 'title_column' => false    , 'required' => false, 'unique' => false, 'locked'=>true ),
            array( 'name' => 'Searchable'  , 'slug' => 'searchable'    , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'boolean'       , 'extra' => array('default_value' => 1, 'false_text'=>'No','true_text'=>'Yes') , 'title_column' => false    , 'required' => false, 'unique' => false, 'locked'=>true ),
            array( 'name' => 'Tax ID'      , 'slug' => 'tax_id'        , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'relationship'  , 'extra' => array('choose_stream' =>  $tax_stream_id ) , 'title_column' => false, 'required' => $tax_field_required , 'unique' => false,'locked'=>true ),
            array( 'name' => 'Type ID'     , 'slug' => 'type_id'       , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'integer'    , 'extra' => array('max_length' => 11,'readonly'=>true)              , 'title_column' => false   , 'required' => false , 'unique' => false  , 'locked'=>true ),          
            array( 'name' => 'Type Slug'   , 'slug' => 'type_slug'     , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'text'          , 'extra' => array('max_length' => 200)             , 'title_column' => false    , 'required' => true , 'unique' => false    , 'locked'=>true ),
            array( 'name' => 'Points'      , 'slug' => 'points'       , 'assign'     => 'products', 'namespace' => 'nc_products', 'type' => 'integer'       , 'extra' => array('max_length' => 11)              , 'title_column' => false    , 'required' => false , 'unique' => false   , 'locked' =>true),

        //private $products_type_fields = array(
            array( 'name' => 'Name'         , 'slug' => 'name'         , 'assign'    => 'products_types', 'namespace' => 'nc_products_types', 'type' => 'text'       , 'extra' => array('max_length' => 100)     , 'title_column' => true    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Properties'   , 'slug' => 'properties'   , 'assign'    => 'products_types', 'namespace' => 'nc_products_types', 'type' => 'textarea'   , 'extra' => []                        , 'title_column' => true    , 'required' => false, 'unique' => false , 'locked' =>true),
            array( 'name' => 'Slug'         , 'slug' => 'slug'         , 'assign'    => 'products_types', 'namespace' => 'nc_products_types', 'type' => 'text'       , 'extra' => array('max_length' => 200)     , 'title_column' => true    , 'required' => true , 'unique' => true  , 'locked' =>true)  ,             
            array( 'name' => 'Default'      , 'slug' => 'default'      , 'assign'    => 'products_types', 'namespace' => 'nc_products_types', 'type' => 'integer'    , 'extra' => array('max_length' => 1)       , 'title_column' => true    , 'required' => false, 'unique' => false , 'locked' =>true),
            array( 'name' => 'Core'         , 'slug' => 'core'         , 'assign'    => 'products_types', 'namespace' => 'nc_products_types',  'type' => 'boolean'    , 'extra' => array('default_value' => 0,'dv'=>0,'false_text'=>'No','true_text'=>'Yes')       , 'title_column' => true    , 'required' => false , 'unique' => false , 'locked' =>true),

        //private $products_variances_fields = array(
            array( 'name' => 'Deleted'      , 'slug' => 'deleted'      , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'datetime'   , 'extra' => array('storage' => 'datetime') , 'title_column' => false   , 'required' => false, 'unique' => false , 'locked' =>true),
            array( 'name' => 'Product ID'   , 'slug' => 'product_id'   , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'integer'    , 'extra' => array('max_length' => 11)      , 'title_column' => false    , 'required' => true  , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Name'         , 'slug' => 'name'         , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'text'       , 'extra' => array('max_length' => 100)     , 'title_column' => true    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Min QTY'      , 'slug' => 'min_qty'      , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'integer'    , 'extra' => array('max_length' => 11)      , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Price'        , 'slug' => 'price'        , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2)    , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'RRP'          , 'slug' => 'rrp'          , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2)    , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Base'         , 'slug' => 'base'         , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2)    , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true),
            //array( 'name' => 'Ship Charge'  , 'slug' => 'shipping_each', 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2)    , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Available'    , 'slug' => 'available'    , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'boolean'    , 'extra' => array('default_value' => 1,'dv'=>1, 'false_text'=>'No','true_text'=>'Yes') , 'title_column' => true    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Discountable' , 'slug' => 'discountable' , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'boolean'    , 'extra' => array('default_value' => 1,'dv'=>1, 'false_text'=>'No','true_text'=>'Yes') , 'title_column' => true    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Package Group', 'slug' => 'pkg_group_id' , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'integer'    , 'extra' => array('max_length' => 11)      , 'title_column' => false    , 'required' => true  , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Is Shippable' , 'slug' => 'is_shippable' , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'boolean'    , 'extra' => array('default_value' => 1,'dv'=> 1, 'false_text'=>'No', 'true_text'=>'Yes (Is or has a Shippable component)')        , 'title_column' => true    , 'required' => false , 'unique' => false , 'locked' =>true),
            array( 'name' => 'Is Digital'   , 'slug' => 'is_digital'   , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'boolean'    , 'extra' => array('default_value' => 0,'dv'=> 0, 'false_text'=>'No', 'true_text'=>'Yes (Is or has a Digital component)')         , 'title_column' => true    , 'required' => false , 'unique' => false, 'locked' =>true),
            array( 'name' => 'Height'       , 'slug' => 'height'       , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.00),  'title_column' => false  , 'required' => false, 'unique' => false , 'locked' =>true),
            array( 'name' => 'Width'        , 'slug' => 'width'        , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.00),  'title_column' => false  , 'required' => false, 'unique' => false , 'locked' =>true),
            array( 'name' => 'Length'       , 'slug' => 'length'       , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.00),  'title_column' => false  , 'required' => false, 'unique' => false , 'locked' =>true),
            array( 'name' => 'Weight'       , 'slug' => 'weight'       , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'decimal'    , 'extra' => array('decimalplaces' => 2, 'minvalue' => 0.00),  'title_column' => false  , 'required' => false, 'unique' => false , 'locked' =>true),
            array( 'name' => 'Ship to Zone' , 'slug' => 'zone_id'      , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'integer'    , 'extra' => array('max_length' => 11)      , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true) ,
            array( 'name' => 'SKU'          , 'slug' => 'sku'          , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'text'       , 'extra' => array('max_length' => 100)     , 'title_column' => false    , 'required' => false, 'unique' => false , 'locked' =>true),            
            
            array( 'name' => 'On Hand'      , 'slug' => 'sk_on_hand'   , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'integer'    , 'extra' => array('max_length' => 11)      , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true), 
            //array( 'name' => 'On Order'     , 'slug' => 'sk_on_order'  , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'integer'    , 'extra' => array('max_length' => 11)      , 'title_column' => false    , 'required' => false , 'unique' => false , 'locked' =>true), 
            array( 'name' => 'Back Orders'  , 'slug' => 'sk_backorder' , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'boolean'    , 'extra' => array('default_value' => 0,'dv'=> 0, 'false_text'=>'No', 'true_text'=>'Yes (This allows your customers to place back orders)')         , 'title_column' => false    , 'required' => false , 'unique' => false, 'locked' =>true),                 
                
            //Whare house location
            //array( 'name' => 'WH Location'  , 'slug' => 'location_id'  , 'assign'    => 'products_variances', 'namespace' => 'nc_product_variances', 'type' => 'integer'    , 'extra' => array('max_length' => 11)      , 'title_column' => false    , 'required' => true  , 'unique' => false , 'locked' =>true),            
            
            //states
            array( 'name' => 'Name'        , 'slug' => 'name'          , 'assign'    => 'states'    , 'namespace' => 'nc_states', 'type' => 'text'            , 'extra' => array('max_length' => 100)                     , 'title_column' => true  , 'required' => true , 'unique' => false , 'locked' =>true),  
            array( 'name' => 'Local Code'  , 'slug' => 'code'          , 'assign'    => 'states'    , 'namespace' => 'nc_states', 'type' => 'text'            , 'extra' => array('max_length' => 100)                     , 'title_column' => false  , 'required' => true , 'unique' => false , 'locked' =>true),  
            array( 'name' => 'Country ID'  , 'slug' => 'country_id'    , 'assign'   => 'states'     , 'namespace' => 'nc_states', 'type' => 'relationship'     , 'extra' => array('choose_stream' => $countries_stream,'link_uri'=>NC_ADMIN_ROUTE.'/countries/edit/-id-')    , 'title_column' => false , 'required' => false, 'unique' => false , 'locked' =>true),                                 


        ];

        if($default_product_fields = $this->config->item('install/default_product_fields'))
        {
            $streams_fields[] = [ 'name' => 'Short Description' , 'slug' => 'meta_description'  , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'wysiwyg'  , 'extra' => array('editor_type' => 'simple') , 'title_column' => false , 'required' => false, 'unique' => false ];                        
            $streams_fields[] = [ 'name' => 'Description'       , 'slug' => 'description'       , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'wysiwyg'  , 'extra' => array('editor_type' => 'advanced') , 'title_column' => false , 'required' => false, 'unique' => false ];            
            $streams_fields[] = [ 'name' => 'Code'              , 'slug' => 'code'              , 'assign'    => 'products', 'namespace' => 'nc_products', 'type' => 'text'     , 'extra' => array('max_length' => 100)         , 'title_column' => false , 'required' => false, 'unique' => false ];
        }

        $this->_createStreamFields(  $streams_fields  );

        //set in session
        $this->session->set_userdata('prod_stream_id',$prod_stream_id);
        $this->session->set_userdata('products_variances_stream_id',$products_variances_stream_id);
        $this->session->set_userdata('products_types_stream_id',$products_types_stream_id);     

        return true;
    }

    public function uninstall($installer=NULL)
    {
        $this->_remove_stream('nc_products');
        $this->_remove_stream('nc_product_variances');
        $this->_remove_stream('nc_products_types'); 
        $this->_remove_stream('nc_states');
        return true;
    }

    public function upgrade($installer,$old_version)
    {
        $ncmo = new NCMessageObject();
        return $ncmo;
    }
 

}