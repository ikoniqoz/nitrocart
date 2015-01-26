<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class E_attributes_m extends MY_Model
{
	public $_table = 'nct_e_attributes';

	protected $_description_tags = '<b><div><strong><em><i><u><ul><ol><li><p><span><a><br><br />';

	public function __construct()
	{
		parent::__construct();
	}


    public function get_by_variance_id($variance_id)
    {
        return $this->where( 'e_variance', $variance_id)->get_all();
    }

    public function set_value($id, $y)
    {
        return $this->update($id, ['e_value'=>$y] );
    }


	public function create_templates_from_product_type( $product_type_id , $product_id )
	{

        //creat a e_attribute record for each property
        $r = $this->db->where('id',$product_type_id)->get('nct_products_types')->row();
        if($r)
        {        

            $arr = unserialize( $r->properties );

            foreach ($arr as $key => $value) 
            {
                    $this->db->insert( 'nct_e_attributes', 
                            [
                                'e_product' => $product_id,
                                'e_variance' => NULL,
                                'e_label' => $value->name,
                                'e_value' => '',
                                'e_data' => '', //this could be the list of available options for the set
                                'e_notes' => '',
                            ]
                    );
            }

            return true;
        }

        return false;
	}


	public function create_templates_from_product( $old_prod_id,  $product_id )
	{

        //get all e-attributes,  where, var old_variance_id
        $old_variances = $this->db->where('e_product',$old_prod_id)->where('e_variance',NULL)->get('nct_e_attributes')->result();
        
        foreach($old_variances as $eattrib)
        {
            //create a TEMPLATE e_ATTRIBUTE ROW
            $this->db->insert( 'nct_e_attributes', 
                [
                    'e_product' => $product_id,
                    'e_variance' => NULL,
                    'e_label' => $eattrib->e_label,
                    'e_value' =>  $eattrib->e_value,
                    'e_data' =>  $eattrib->e_data, //this could be the list of available options for the set
                    'e_notes' =>  $eattrib->e_notes,
                ]
            );   
        }
	}


	/**
	 * 
	 */
	public function create_attributes_from_product_template( $product_id , $variance_id )
	{
        //get fom template
        $templs = $this->db->where('e_product',$product_id)->where('e_variance',NULL)->get('nct_e_attributes')->result();
        if($templs)
        {   
            //perhaps cleanup any mess
            $this->db->where('e_product',$product_id)->where('e_variance',$variance_id)->delete( 'nct_e_attributes' );


            foreach ($templs as $key => $templ) 
            {
                //create a TEMPLATE e_ATTRIBUTE ROW
                $this->db->insert( 'nct_e_attributes', 
                            [
                                'e_product' => $product_id,
                                'e_variance' => $variance_id,
                                'e_label' => $templ->e_label,
                                'e_value' => $templ->e_value,
                                'e_data' =>  $templ->e_data, //this could be the list of available options for the set
                                'e_notes' => $templ->e_notes,
                            ]
                );

            }
        }

        return true;     
	}


	public function create_attributes_from_duplicate_product( $old_prod_id, $old_variance_id, $new_prod_id , $new_var_id )
	{

        //get all e-attributes,  where, var old_variance_id
        $old_variances = $this->db->where('e_product',$old_prod_id)->where('e_variance',$old_variance_id)->get('nct_e_attributes')->result();
        
        foreach($old_variances as $eattrib)
        {
            //create a TEMPLATE e_ATTRIBUTE ROW
            $this->db->insert( 'nct_e_attributes', 
                [
                    'e_product' => $new_prod_id,
                    'e_variance' => $new_var_id,
                    'e_label' => $eattrib->e_label,
                    'e_value' =>  $eattrib->e_value,
                    'e_data' =>  $eattrib->e_data, //this could be the list of available options for the set
                    'e_notes' =>  $eattrib->e_notes,
                ]
            );   
        }

        return true;     
	}

	public function create_attributes_from_duplicate_product_variance($product_id, $old_variance_id, $new_var_id )
	{
		
        //get all e-attributes,  where, var old_variance_id
        $eattribs = $this->db->where('e_product', $product_id)->where('e_variance',$old_variance_id)->get('nct_e_attributes')->result();
        
        foreach($eattribs as $eattrib)
        {
            //create a TEMPLATE e_ATTRIBUTE ROW
            $this->db->insert( 'nct_e_attributes', 
                [
                    'e_product' => $product_id,
                    'e_variance' => $new_var_id,
                    'e_label' => $eattrib->e_label,
                    'e_value' =>  $eattrib->e_value,
                    'e_data' =>  $eattrib->e_data, //this could be the list of available options for the set
                    'e_notes' =>  $eattrib->e_notes,
                ]
            );   
        }   

        return true;
	}

	/**
	 * When a variance is dleted we can remove the attribute values for that variance
	 * All order data should be kept in the order file
	 */
	public function product_delete($product_id)
	{
        return $this->db->where('e_product',$product_id)->delete('nct_e_attributes');  
	}
	public function variance_delete($variance_id)
	{
        return $this->db->where('e_variance',$variance_id)->delete('nct_e_attributes');  
	}

  
}