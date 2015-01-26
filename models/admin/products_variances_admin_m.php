<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Products_variances_admin_m extends MY_Model
{


    public $_table = 'nct_products_variances';

    protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('nitrocart/e_attributes_m');
    }


    public function create_standard($product_id , $input)
    {
        $def_zone_id = $this->getDefaultShippingZone();

        //clean price
        $price = (isset($input['price']))?$input['price']:0.00;
        $name = (isset($input['name']))?$input['name']:'Standard';

        $pkg_group_id=(isset($input['pkg_group_id']))?$input['pkg_group_id']:NULL;
        $zone_id= (isset($input['zone_id']))?$input['zone_id']: $def_zone_id ;
        $type_id= (isset($input['type_id']))?$input['type_id']:0;

        //this only exposed when adding a new produt
        //when a new product, there are no conflicting variances
        $to_insert = array(
                'product_id'    => $product_id,
                'name'          => $name,
                'price'         => ($price==null)?0:$price,
                'base'          => 0,
                'rrp'           => ($price==null)?0:$price,
                'available'     => 1,
                'discountable'  => 1,
                'height'        => 1.5,
                'width'         => 1.5,
                'length'        => 1.5,
                'weight'        => 0.4,
                'sku'           => '',
                'is_shippable'  => 1,
                'is_digital'    => 0,                
                'zone_id'        => $zone_id,
                'pkg_group_id'  => $pkg_group_id,
                //the default fields will add their own data
                'created_by'    => $this->current_user->id,
                'created'       => date("Y-m-d H:i:s"),
                'updated'       => date("Y-m-d H:i:s"),
        );

        // Now insert
        $id = $this->insert($to_insert);

        $done = $this->e_attributes_m->create_templates_from_product_type( $type_id , $product_id );

        if($id)
        {   
            $this->e_attributes_m->create_attributes_from_product_template( $product_id , $id );
        }

        // return id
        return ($id)? $id : false;
    }

    public function getDefaultShippingZone()
    {
        //get the default shpping zone id
        if($row = $this->db->where('default',1)->get('nct_zones')->row())
        {
            return $row->id;
        }   
        return 0;
    }


    public function create($product_id , $input=[])
    {

        $returnArray = $this->returnObject();

        $variance_name = (isset($input['name']))?$input['name']:'Standard';
        $sku = (isset($input['sku']))?$input['sku']:'';

        $to_insert = array(
                'product_id'    => $product_id,
                'name'          => $variance_name, //$variance_name,
                'sku'           => $sku,
                'price'         => 0, //($input['price']==NULL)?0:$input['price'],
                'base'          => 0, //($input['base']==NULL)?0:$input['base'],
                'rrp'           => 0, //($input['rrp']==NULL)?0:$input['rrp'],
                'height'        => 0, //($input['height']==NULL)?1:$input['height'],
                'width'         => 0, //($input['width']==NULL)?1:$input['width'],
                'length'        => 0, //($input['length']==NULL)?1:$input['length'],
                'weight'        => 0, //($input['weight']==NULL)?1:$input['weight'],
                'zone_id'       => 0, //($input['zone_id']==NULL)?0:$input['zone_id'],
                'available'     => 0, //$input['available'],
                'discountable'  => 1, //$input['discountable'],
                'pkg_group_id'  => NULL, //$input['pkg_group_id'],
                'is_shippable'  => 1,
                'is_digital'    => 0,
                'created_by'    => $this->current_user->id,
                'created'       => date("Y-m-d H:i:s"),
                'updated'       => date("Y-m-d H:i:s"),
        );

        $id = $this->insert($to_insert);

        if($id)
        {            
            $this->e_attributes_m->create_attributes_from_product_template( $product_id , $id );
            $returnArray['id'] = $id;
            $returnArray['status'] = JSONStatus::Success;
            $returnArray['message'] = "Successfully updated variance {$variance_name}";
            $returnArray['record']  = $this->get($id);
        }
        else
        {
            $returnArray['message'] = 'Create Error. This product already has a variance of this type. ';
        }

        return $returnArray;
    }


    public function edit($product_id , $variance_id, $input=[])
    {
        //By default return status == Error
        $returnArray = $this->returnObject();

        if( $orig = $this->get($variance_id) )
        {

            $name = (isset($input['name']))?$input['name']:$orig->name;

            $to_update = ['updated'       => date("Y-m-d H:i:s") ];
            $to_update['name'] = $name;
            $to_update['sku'] = (isset($input['sku']))?$input['sku']:$orig->sku;
            $to_update['is_shippable'] = (isset($input['is_shippable']))?(int)$input['is_shippable']:$orig->is_shippable;
            $to_update['is_digital'] =  (isset($input['is_digital']))?(int)$input['is_digital'] :$orig->is_digital;

            if($id = $this->update($variance_id, $to_update))
            {            
                $returnArray['message'] = "Successfully updated {$name}.";
                $returnArray['status']  = JSONStatus::Success;
                $returnArray['id']      = $id;
                $returnArray['record']  = $this->get($variance_id); 
            }
            else
            {
                $returnArray['message'] = "Unable to update this variance.";
            }

        }

        return $returnArray;
    }


    /**
     * Simple toggle of variance
     * returns the current status after the toggle
     */
    public function edit_available($variance_id)
    {
        //get thy self        
        $variant = $this->get($variance_id);
        $variant->available = ($variant->available ==0)?1:0;
        return $this->edit_single($variance_id, 'available' , $variant->available);
    }
    public function toggle_shippable($variance_id)
    {  
        $variant = $this->get($variance_id);
        return $this->edit_single($variance_id, 'is_shippable' , (!($variant->is_shippable)) );
    }    
    
    public function edit_discountable($variance_id)
    {
        //get thy self        
        $variant = $this->get($variance_id);
        $variant->discountable = ($variant->discountable ==0)?1:0;
        return $this->edit_single($variance_id, 'discountable' , $variant->discountable);
    }
    
    public function edit_single($variance_id, $field_name='name' , $field_value ='')
    {

        //By default return status == Error
        $returnArray = $this->returnObject();

        //get thy self        
        $variant = $this->get($variance_id);

        $variant->$field_name = $field_value;


        $to_update = array(
                $field_name          => $field_value,
        );

        if($id = $this->update($variance_id, $to_update))
        {          
            $returnArray['message'] = "Successfully updated availability.";
            $returnArray['status']  = JSONStatus::Success;
            $returnArray['id']      = $id;
            $returnArray['record']  = $variant;            
        }
        else
        {
            $returnArray['message'] = "Unable to update this variance.";
        }

        return $returnArray;
    } 

    public function edit_price($variance_id, $input=[])
    {
        //By default return status == Error
        $returnArray = $this->returnObject();

        $to_update = array(
                'price'         => ($input['price']==NULL)?0:$input['price'],
                'base'          => ($input['base']==NULL)?0:$input['base'],
                'rrp'           => ($input['rrp']==NULL)?0:$input['rrp'],
                'discountable'  =>  $input['discountable'],
                'updated'       => date("Y-m-d H:i:s"),
        );

        if($id = $this->update($variance_id, $to_update))
        {                  
            $returnArray['message'] = "Successfully updated.";
            $returnArray['status']  = JSONStatus::Success;
            $returnArray['id']      = $id;
            $returnArray['record']  = $this->get($variance_id);            
        }
        else
        {
            $returnArray['message'] = "Unable to update this variance.";
        }

        return $returnArray;
    }

    public function edit_shipping( $variance_id, $input=[])
    {
        //By default return status == Error
        $returnArray = $this->returnObject();

        $to_update = array(
                'pkg_group_id'  =>  $input['pkg_group_id'],
                'height'        => ($input['height']==NULL)?0:$input['height'],
                'width'         => ($input['width']==NULL)?0:$input['width'],
                'length'        => ($input['length']==NULL)?0:$input['length'],
                'weight'        => ($input['weight']==NULL)?0:$input['weight'],
                'zone_id'       => ($input['zone_id']==NULL)?0:$input['zone_id'],
                'updated'       => date("Y-m-d H:i:s"),
        );

        if($id = $this->update($variance_id, $to_update))
        {
            $returnArray['message'] = "Successfully updated.";
            $returnArray['status']  = JSONStatus::Success;
            $returnArray['id']      = $id;
            $returnArray['record']  = $this->get($variance_id);
        }
        else
        {
            $returnArray['message'] = "Unable to update this variance.";
        }

        return $returnArray;
    }

    /**
     *
     * This is duplicate a product, not duplicate a variance type
     */
    public function duplicate($old_id,$new_id)
    {
        //there should be no conflicting variances as this will be a new product

        // Get the original product
        $rows = $this->get_by_product($old_id);

        
        ///first , make a copy of the template records
        $this->e_attributes_m->create_templates_from_product( $old_id,  $new_id );


        foreach($rows AS $row)
        {
            $old_variance_id = $row->id;

            $to_insert = array(
                    'product_id'    => $new_id,
                    'name'          =>  $row->name,
                    'price'         =>  $row->price,
                    'base'          =>  $row->base,
                    'rrp'           =>  $row->rrp,
                    'available'     =>  $row->available,
                    'discountable'  =>  $row->discountable,
                    'height'        =>  $row->height,
                    'width'         =>  $row->width,
                    'length'        =>  $row->length,
                    'weight'        =>  $row->weight,
                    'zone_id'       =>  $row->zone_id,
                    'sku'           =>  $row->sku,
                    'is_shippable'  =>  $row->is_shippable,
                    'is_digital'    =>  $row->is_digital,
                    'pkg_group_id'  =>  $row->pkg_group_id,
                    'created_by'    =>  $this->current_user->id,
                    'created'       =>  date("Y-m-d H:i:s"),
                    'updated'       =>  date("Y-m-d H:i:s"),
            );

            if($new_var_id = $this->insert($to_insert))
            {
                //now duplicate all the e_attribute rows
                $this->e_attributes_m->create_attributes_from_duplicate_product( $old_id, $old_variance_id, $new_id , $new_var_id );          
            }
        }

        return true;
    }

    public function duplicate_self($self_id)
    {
        $returnArray = $this->returnObject();
        // Get the original product
        $selfy = $this->get($self_id);

        $to_insert = array(
                'product_id'    => $selfy->product_id,
                'name'          =>  $selfy->name,
                'price'         =>  $selfy->price,
                'base'          =>  $selfy->base,
                'rrp'           =>  $selfy->rrp,
                'available'     =>  0, //$selfy->available,
                'discountable'  =>  $selfy->discountable,
                'height'        =>  $selfy->height,
                'width'         =>  $selfy->width,
                'length'        =>  $selfy->length,
                'weight'        =>  $selfy->weight,
                'zone_id'       =>  $selfy->zone_id,
                'sku'           =>  $selfy->sku,
                'is_shippable'  =>  $selfy->is_shippable,
                'is_digital'    =>  $selfy->is_digital,
                'pkg_group_id'  =>  $selfy->pkg_group_id,
                'created_by'    =>  $this->current_user->id,
                'created'       =>  date("Y-m-d H:i:s"),
                'updated'       =>  date("Y-m-d H:i:s"),
        );

        
        if($new_var_id = $this->insert($to_insert))
        {
            $this->e_attributes_m->create_attributes_from_duplicate_product_variance($selfy->product_id, $selfy->id, $new_var_id );

            $to_insert['id'] = $new_var_id;

            $returnArray['message'] = "Successfully duplicated.";
            $returnArray['status']  = JSONStatus::Success;
            $returnArray['id']      = $new_var_id;
            $returnArray['record']  = $to_insert;
        }
        else
        {
            $returnArray['status']  = JSONStatus::Error;
            $returnArray['message'] = "Unable to duplicate this variance.";
        }

        return $returnArray;
    }

    public function delete_by_product($product_id,$mode='hard')
    {
        $this->e_attributes_m->product_delete($product_id);        

        if($mode=='hard')
            $this->update_by( 'product_id',$product_id, array('deleted'=> date("Y-m-d H:i:s") ));

        return true;
    }

    public function get_by_product($product_id)
    {
        return $this->where('product_id',$product_id)->where('deleted',NULL)->get_all();
    }

    public function delete($variance_id)
    {
        $this->e_attributes_m->variance_delete($variance_id);
        return $this->update($variance_id, array('deleted'=> date("Y-m-d H:i:s") ));
    }


    private function returnObject()
    {
        $retArray = [];
        $retArray['status']     = JSONStatus::Error;
        $retArray['message']    = '';
        $retArray['id']         = '';
        return $retArray;
    }

}