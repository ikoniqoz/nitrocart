<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/../orders_m.php');
class Orders_admin_m extends Orders_m
{
	protected $_table_order_items = "nct_order_items";

	public function __construct() {
		parent::__construct();
	}


	/**
	 * Delete an order is not a normal thing to do,
	 * we need to ask confirmation first.
	 * @return [type] [description]
	 * Do not let people delete orders, find a way to make this secure but have this functionaility for super admins or something
	 */
    public function delete($order_id) {
        $u_id = $this->current_user->id;
        $delete_message = "Admin {$u_id} deleted order"; 
        return $this->_delete($order_id, $delete_message);
    }

     /**
     * Delete an order is not a normal thing to do,
     * we need to ask confirmation first.
     * @return [type] [description]
     * Do not let people delete orders, find a way to make this secure but have this functionaility for super admins or something
     */
    public function _delete($order_id, $delete_message) {
        $deleted_datetime = date("Y-m-d H:i:s");
        //load req models
        $this->load->model('nitrocart/transactions_m');
        $this->update_by( 'id', $order_id, ['deleted'=> $deleted_datetime , 'delete_message'=>$delete_message ]);
        //$this->db->update($this->_table_order_items, ['deleted'=> $deleted_datetime ]);
        $this->transactions_m->log($order_id, 0,  0 , $this->current_user->username, $delete_message, 'accepted');
        return true;
    }
    
	public function get_invoices_by_order($id)
	{
		return $this->db->where('order_id', $id)->get('nct_order_invoice')->result();
	}


	/**
	 * Creates a note on a order - This is only visible to admins, do not getthis confused with messages
	 */
	public function create_note($order_id,$user_id, $message) {
		$contents = [
        	'order_id' 		=> $order_id,
			'user_id' 		=> $user_id,
			'message' 		=> $message,
			'date' 			=> time()
		];

		$this->db->insert('nct_order_notes', $contents);

		return $order_id;
	}


	public function get_all()
	{
		return parent::get_all();
	}

    public function get_all_admin_list()
    {
        $this->db->select('nct_orders.*');
        $this->db->select('addr.email as customer_email');
        $this->db->select('CONCAT(addr.first_name, " ", addr.last_name) as customer_name', false);
        $this->db->select('addr.city AS city', false);
        $this->db->join('nct_addresses addr', 'nct_orders.billing_address_id = addr.id', 'left');
        $this->db->group_by('nct_orders.id');
        return parent::get_all();
    }

    public function get_most_recent($limit=5,$offset=0)
    {
        return $this->limit($limit,$offset)->order_by('paid_date','desc')->get_all_admin_list(); 
        //$this->limit($limit,$offset)->order_by('order_date','desc')->get_all();
    }    

	/**
	 * Get the information from a users profile, if guest return an pre-defined array merged with the contact info.
	 *
	 * @param  [type] $user_id         [description]
	 * @param  array  $billing_address This is only used for guest customers. Some of the data is used to populate info
	 * @return [type]                  [description]
	 */
	public function get_user_data($user_id, $billing_address = [] )
	{
		if($user_id == 0)
		{
			$guest 			         = new ViewObject();
			$guest->id 		         = 0;
			$guest->created          = 0;
			$guest->updated          = 0;
			$guest->created_by       = 0;
			$guest->ordering_count   = 0;
			$guest->user_id          = 0;
			$guest->display_name     = $billing_address->first_name;
			$guest->first_name       = $billing_address->first_name;
			$guest->last_name        = $billing_address->last_name;
			$guest->bio 	         = '';
			$guest->dob 	         = '';
			$guest->gender 	         = NULL;
			$guest->updated_on       = NULL;
			$guest->is_guest         = true;
			return $guest;
		}

		$profile =  $this->db->where('user_id', $user_id)->get('profiles')->row();
		$profile->is_guest = false;
		return $profile;
	}



    protected function _prepare_filter($filter = [])
    {
        $my_filter = [];
        
        if (array_key_exists('f_order_status', $filter))
        {
            switch($filter['f_order_status'])
            {
                case 'all': 
                    break;                   
                default:
                    $my_filter['f_order_status']['action'] = 'where';
                    $my_filter['f_order_status']['key'] = 'nct_orders.status_id';
                    $my_filter['f_order_status']['value'] = $filter['f_order_status'];
                    break;                                             
            }

        }  
        if (array_key_exists('status', $filter))
        {
            switch($filter['status'])
            {
                case 'active':
                    $my_filter['status']['action'] = 'where';
                    $my_filter['status']['key'] = 'nct_orders.deleted';
                    $my_filter['status']['value'] = NULL;
                    break;
                case 'deleted':
                    $my_filter['status']['action'] = 'where';
                    $my_filter['status']['key'] = 'nct_orders.deleted !=';
                    $my_filter['status']['value'] = 'NULL';
                    break;   
                case 'all':                    
                default:
                    //do nothing
                    break;                                                             
            }

        }
        if (array_key_exists('f_payment_status', $filter))
        {
            switch($filter['f_payment_status'])
            {
                case 'unpaid':
                    $my_filter['f_payment_status']['action'] = 'where';
                    $my_filter['f_payment_status']['key'] = 'nct_orders.paid_date';
                    $my_filter['f_payment_status']['value'] = NULL;
                    break;
                case 'paid':
                    $my_filter['f_payment_status']['action'] = 'where';
                    $my_filter['f_payment_status']['key'] = 'nct_orders.paid_date !=';
                    $my_filter['f_payment_status']['value'] = 'NULL';
                    break;   
                case 'all':                    
                default:
                    //do nothing
                    break;                                                             
            }

        }
        if (array_key_exists('f_keyword_search', $filter))
        {
            if(trim($filter['f_keyword_search'] != ''))
            {
                $my_filter['status']['action'] = 'where';
                $my_filter['status']['key'] = 'nct_orders.id';
                $my_filter['status']['value'] = (int) $filter['f_keyword_search'];                              
            }
        }
        if (array_key_exists('f_order_by', $filter))
        {

            $my_filter['f_order_by']['action'] = 'order_by';
            $my_filter['f_order_by']['key'] = 'nct_orders.' . trim($filter['f_order_by']);
            $my_filter['f_order_by']['value'] = 'asc' ;                              
        
	        if (array_key_exists('f_order_by_dir', $filter))
	        {
	            if(trim($filter['f_order_by_dir'] != ''))
	            {
	                $my_filter['f_order_by']['value'] = $filter['f_order_by_dir'];                              
	            }
	        }            
        }


        return $my_filter;
    }

    public function filter_count($filter = [])
    {
        $this->reset_query();
        $new_filters = $this->_prepare_filter($filter);
    

        //where+like
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if(($action=='where')|| ($action=='like'))
                $this->$action($key,$value);
        }

        //order bys
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if($action=='order_by')
                $this->$action($key,$value);
        }      

        $this->from($this->_table);

        return $this->count_all_results();

    }	

    public function filter($filter=[] , $limit=5, $offset = 0)
    {
        $this->reset_query();
        $new_filters = $this->_prepare_filter($filter);

        //where+like
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if(($action=='where')|| ($action=='like'))
                $this->$action($key,$value);
        }

        //order bys
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if($action=='order_by')
                $this->$action($key,$value);
        }      

        $this->limit( $limit , $offset );

        return $this->get_all_admin_list();
    }
}