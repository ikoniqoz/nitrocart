<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Products_admin_m extends MY_Model
{

    /**
     * The default table for this model
     * @var string
     */
    public $_table = 'nct_products';
    protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';

    public $_create_validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'lang:name',
                'rules' => 'trim|max_length[100]|required'
            ),
        );

    public $_edit_validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'lang:name',
                'rules' => 'trim|max_length[100]|required'
            ),
            array(
                'field' => 'slug',
                'label' => 'Slug',
                'rules' => 'trim'
            ),
            array(
                'field' => 'ordering_count',
                'label' => 'Ordering Count',
                'rules' => 'trim|numeric'
            ),

            array(
                'field' => 'featured',
                'label' => 'Featured',
                'rules' => 'trim|numeric|required'
            ),
            array(
                'field' => 'searchable',
                'label' => 'Searchable',
                'rules' => 'trim|numeric|required'
            ),
            array(
                'field' => 'public',
                'label' => 'Visibility',
                'rules' => 'trim|numeric|required'
            ),

    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('nitrocart/tax_m');
        $this->load->helper('nitrocart/nitrocart_admin');
    }

    public function first_by_tax_id($tax_id)
    {
        return $this->db->where('tax_id',$tax_id)->where('deleted',NULL)->get('nct_products')->row();
    }


    /**
     * Get a product either by numeric ID or SLUG
     * @return MIXED - Object or false
     */
    public function get_product($id)
    {
        $method = (is_numeric($id))?'id':'slug';
        $product = parent::get_by( array($method => $id) );
        return ($product)?$product:false;
    }


    /**
     * Get all public and non deleted products
     * @return Array Products Array
     */
    public function get_all()
    {
        return parent::get_all();
    }

    /**
     * get the next id in the list
     */
    public function get_next($prod_id)
    {
        $row = $this->db->order_by('id','asc')->where('id >', $prod_id)->get($this->_table)->row();
        return ($row)?$row->id : $this->get_first_id();
    }

    /**
     * Get the previous ID in the list
     */
    public function get_prev($prod_id)
    {
        $row = $this->db->order_by('id','desc')->where('id <', $prod_id)->get($this->_table)->row();
        return ($row)?$row->id : $this->get_last_id();
    }

    /**
     * Get the first ID of all, either active or deleted
     */
    public function get_first_id()
    {
        //now just try to get the first
        $row = $this->db->order_by('id','asc')->get($this->_table)->row();
        return ($row)? $row->id : 0 ;
    }

    /**
     * Get the last ID of all, either active or deleted
     */
    public function get_last_id()
    {
        //now just try to get the last
        $row = $this->db->order_by('id','desc')->get($this->_table)->row();
        return ($row)? $row->id : 0 ;

    }



    /**
     * Create a new product, only some fields are required, the rest uses the default fields,
     * when creating a new product, you must first enter the first few req values, save-> then edit the newly created product.
     *
     * The TYPE_ID is set once and can not be altered.
     *
     * @param Array $input Input fields from user, they should be prepped before coming here.
     */
    public function create($input)
    {
        $_name = strip_tags($input['name']);

        //$new_slug = $this->get_unique_slug( $_name );
        $new_slug = $this->get_new_unique_slug( $_name );

        
        $type_id= (isset($input['type_id']))?$input['type_id']:0;
        $type_slug= (isset($input['type_slug']))?$input['type_slug']:'';
        $public= (isset($input['public_override']))?1:0;


        // We need to confdition this to see if it is valid
        if($prod_type = $this->db->where('id', $type_id )->get('nct_products_types')->row())
        {
            $type_id = $prod_type->id;
            $type_slug = $prod_type->slug;
        }


        $to_insert = array(
                'name'          => $_name,
                'slug'          => $new_slug,
                'featured'      => 0,
                'searchable'    => 1,
                'public'        => $public,
                'created_by'    => $this->current_user->id,
                'created'       => date("Y-m-d H:i:s"),
                'updated'       => date("Y-m-d H:i:s"),
                'type_id'       => $type_id,          
                'type_slug'     => $type_slug,              
        );

        if(isset($input['tax_id']))
        {
            $to_insert['tax_id'] = ($input['tax_id']=='')?NULL:$input['tax_id'];
        }

        $new_product_id =  $this->insert($to_insert);

        if($new_product_id)
        {
            $this->load->model('nitrocart/admin/products_variances_admin_m');
            $this->products_variances_admin_m->create_standard($new_product_id , $input  );

            //Notify ALL about the new product
            Events::trigger('SHOPEVT_AdminProductCreate', $new_product_id);

        }

        return $new_product_id;
    }


    /**
     *
     * @param unknown_type $id The ID of the original product to duplicate
     * Should we create duplicate price records ?? or letthe admin create new ones ?
     */
    public function duplicate($id)
    {

        // Get the original product
        $product = $this->get($id);

        if ($product==NULL) return false;

        $new_slug = $this->get_new_unique_slug( "{$product->name}" );

        $to_insert = [];
        foreach($product as $key =>$value)
        {
            $to_insert[$key] = $value;
        }

        //remove id
        if(isset( $to_insert['id'] )) unset( $to_insert['id'] );

        //def core value override
        $to_insert['created'] = date("Y-m-d H:i:s");
        $to_insert['updated'] = date("Y-m-d H:i:s");
        $to_insert['views'] = 0;
        $to_insert['public'] = 0;
        $to_insert['public'] = 0;
        $to_insert['slug'] = $new_slug;
        $to_insert['created_by'] = $this->current_user->id;

        $new_id =  $this->insert($to_insert); //returns id

        if($new_id)
        {

            $this->load->model('nitrocart/admin/products_variances_admin_m');
            $this->products_variances_admin_m->duplicate($id,$new_id);

            //Let other modules know to copy the data too
            Events::trigger('SHOPEVT_AdminProductDuplicate', array('OriginalProduct'=>$id , 'NewProduct'=>$new_id) );
        }

        return $new_id;
    }


    /**
     * Admin function
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function delete($product_id)
    {
        //get the product to delete, so we have some meta data
        $_p = $this->get_product($product_id);

        //get the date formatted so we record in the slug when it was deleted. We do have a deleted field but this is useful too
        $date = getdate();
        $datew = $date['year'].'-'.$date['mon'].'-'.$date['mday'];

        $delte_slug = md5($product_id);
        //get a unique deleted slug
        $new_deleted_slug = $this->get_unique_slug($delte_slug,$product_id);

        //also update slug so the original it can be re-used
        $del_status =  $this->update($product_id, array('slug'=>$new_deleted_slug, 'deleted' => date("Y-m-d H:i:s"), 'public' => 0 ) );
        if($del_status)
        {

            //no longer delete variance items when produt is deleted
            //$this->load->model('nitrocart/admin/products_variances_admin_m');
            //$this->products_variances_admin_m->delete_by_product($product_id);
            //However we need to clean up the attributes table
            $this->load->model('nitrocart/admin/products_variances_admin_m');
            $this->products_variances_admin_m->delete_by_product($product_id,'soft');            
            Events::trigger('SHOPEVT_AdminProductDelete', $product_id);
        }
        return $del_status;
    }


        /**
     * Admin function
     * @param  [type] $product_id [description]
     * @return [type]             [description]
     */
    public function set_visibility($product_id, $new_status=1)
    {
        $status =  $this->update($product_id, array('public' => $new_status, 'updated'=> date("Y-m-d H:i:s") ) );
        return $status;
    }
    public function set_searchable($product_id, $new_status=1)
    {
        $status =  $this->update($product_id, array('searchable' => $new_status, 'updated'=> date("Y-m-d H:i:s") ) );
        return $status;
    }
    public function set_featured($product_id, $new_status=1)
    {
        $status =  $this->update($product_id, array('featured' => $new_status, 'updated'=> date("Y-m-d H:i:s") ) );
        return $status;
    }


    /**
     * Override MY_Model as we have hidden and deleted
     *
     * @return [type] [description]
     */
    public function count_all()
    {
        $filter = [];
        $count = $this->count_by($filter);
        return $count;
    }


    /**
     * Get a unique slug for the products table : edit
     *
     * This is the prefered for edits
     */
    protected function get_unique_slug($slug, $id = -1, $prefix = '')
    {
        // 1
        $slug = (trim($slug) == "") ? $prefix.$slug : $slug ;

        // 2
        $slug = shop_slugify($slug);

        // 3
        $slug_count = $this->db->where('id !=',$id)->where('slug', $slug )->get( $this->_table )->num_rows();

        //4.
        return ($slug_count > 0) ? $this->get_unique_slug(  ($slug.'-'.$slug_count)  , $id, $prefix) :  $slug;
    }

    /**
     * should only be used for new slugs/duplicate
     * This is the prefered for new products and duplicate method
     */
    protected function get_new_unique_slug( $slug = '', $count = 1,$first=true)
    {

        $slug = shop_slugify($slug);


        $test_slug = ( $first==true ) ? $slug : $slug.'-'.$count ;


        $new_count = $this->db->where('slug', $test_slug )->get( $this->_table )->num_rows();

        if ( $new_count > 0 )
        {
            return $this->get_new_unique_slug(  $slug  , ($count+1) , false);
        }

        return $test_slug;
 
    }

}