<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Customers_m extends MY_Model 
{


    public $_table = 'nct_customers';


    public function __construct()
    {
        parent::__construct();    
    }

 
    public function record($user_id, $first_name,$order_id)
    {
        if($this->has_account($user_id))
        {
            //update
            return $this->_update($user_id,$order_id);            
        }
        else
        {
            //create
            return $this->create($user_id,$first_name);
        }

        return false;
    }

    /**
     * Type can only be billing or shipping
     *
     * @param  [type] $input [description]
     * @param  string $type  [description]
     * @return [type]        [description]
     */
    public function create($user_id, $first_name)
    {
        //must check if not exist.
        $this->db->trans_start();
        $to_insert = [
                'user_id'       => $user_id,
                'first_name'    => $first_name,
                'updated'       => date("Y-m-d H:i:s"),
                'created'       => date("Y-m-d H:i:s"),
        ];

        $id = $this->insert($to_insert);
        $this->db->trans_complete();
        return ($this->db->trans_status() === false) ? false : $id;
    }

    public function _update($user_id, $order_id)
    {

        //must check if not exist.
        $this->db->trans_start();
        $to_insert = array(
                'user_id'       => $user_id,
                'last_order'    => $order_id,
                'updated'       => date("Y-m-d H:i:s"),
        );

        $id =  $this->db->where('user_id',$user_id)->update($this->_table,$to_insert);
        $this->db->trans_complete();
        return ($this->db->trans_status() === false) ? false : $id;
    }


    public function has_account($user_id)
    {
        $row = $this->db->where('user_id',$user_id)->get($this->_table)->row();
        if($row)
        {
            return $row->id;
        }

        return false;
    }


    public function filter_count($filter_text='')
    {

        $this->db->reset_query();
        $filter_text = strtolower($filter_text);
        $this->db->select('profiles.*,nct_customers.signup_email');
        $this->db->from('profiles');
        $this->db->join('nct_customers', 'nct_customers.user_id = profiles.user_id');

        if($filter_text != '')
        {
            $this->db
                ->like('profiles.first_name',$filter_text)
                ->or_like('profiles.last_name',$filter_text)
                ->or_like('nct_customers.signup_email',$filter_text);
        }
        return $this->db->count_all_results();
    }


    public function filter($filter_text='',$limit=0,$offset=0)
    {
        $this->db->reset_query();
        $filter_text = strtolower($filter_text);
        $this->db->select('profiles.*,nct_customers.signup_email');
        $this->db->from('profiles');
        $this->db->join('nct_customers', 'nct_customers.user_id = profiles.user_id');

        if($filter_text != '')
        {
            $this->db
                ->like('profiles.first_name',$filter_text)
                ->or_like('profiles.last_name',$filter_text)
                ->or_like('nct_customers.signup_email',$filter_text);
        }

        return $this->db->limit( $limit )->offset( $offset )->get()->result();       

    }
   
}