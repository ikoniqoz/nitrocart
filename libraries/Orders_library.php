<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

use Nitrocart\Exceptions\OrderNotFoundException as OrderNotFoundException;

class Orders_library extends ViewObject
{

	public function __construct($params = array())
	{
		log_message('debug', "Orders Library Class Initialized");
	}

	public function clear_admin_orders_filter()
	{
		$this->session->unset_userdata('display_orders_f_order_by_dir_filter');	
		$this->session->unset_userdata('display_orders_f_status_filter');		
		$this->session->unset_userdata('display_orders_f_keyword_search_filter');		
		$this->session->unset_userdata('display_orders_f_order_by_filter');	
		$this->session->unset_userdata('display_f_display_count_filter');		
		$this->session->unset_userdata('display_f_payment_status_filter');		
		$this->session->unset_userdata('display_f_order_status_filter');			
		$this->session->unset_userdata('display_f_filter_status_filter');	
	}


	public function get_admin_order($id)
	{
		
		$this->load->model('nitrocart/addresses_m');		
		$this->load->model('nitrocart/admin/orders_admin_m');		

		$this->data = new ViewObject();

		// Get the order
		if(!$this->data->order = $this->orders_admin_m->get($id))
		{
			throw new OrderNotFoundException('Order doesnt exist!',100);
		}


		// Order Contents
		$this->data->contents = $this->get_order_items($id);
		$this->data->invoice_items = $this->orders_admin_m->get_invoices_by_order($id);
		
		if( ! $this->data->contents)
		{
			//ok we have no contents but we shouldnt fail
			//throw new OrderNotFoundException('Order doesnt exist!',100);
		}

		if( ! $this->data->invoice_items )
		{
			//ok we have no contents but we shouldnt fail
			//throw new OrderNotFoundException('Order doesnt exist!',100);
		}


	 	if($this->data->order->has_shipping_address > 0)
	 	{
			$this->data->shipping_address = $this->_getAddressObject($this->data->order->shipping_address_id);
	 	}
	 	else
	 	{
	 		$this->data->shipping_address = $this->_getAddressObject($this->data->order->billing_address_id);
	 	}


	 	//if(($this->data->order->has_shipping_address > 0) AND ($this->data->order->shipping_address_id > 0))
	 	if($this->data->order->shipping_address_id > 0)
		{	
			// Shipping Method ID
			$this->data->shipping_method 	= $this->_getCheckoutMethodData('shipping', $this->data->order->shipping_id );
	 	}
	 	else
	 	{
			// Shipping Method ID
			$this->data->shipping_method 	= $this->no_ship();
	 	}					 	


		// Get Billing Address
		$this->data->invoice 	= $this->_getAddressObject($this->data->order->billing_address_id);
		$this->data->payments 	= $this->_getCheckoutMethodData('gateway', $this->data->order->gateway_id );  




		// Get All transaction history
		$this->data->transactions = $this->db->where('order_id', $id)->order_by('id desc')->get('nct_transactions')->result();

		$this->data->notes = $this->db->where('order_id',$id)->order_by('id desc')->get('nct_order_notes')->result();

		// Get User Details
		if($this->data->customer = $this->orders_admin_m->get_user_data($this->data->order->user_id,  $this->data->invoice ))
		{
				//Cleanup options
				$this->load->model('nitrocart/workflows_m');
				$this->data->order->current_status = $this->workflows_m->get( $this->data->order->status_id );
				$percent_value = ($this->data->order->current_status) ? $this->data->order->current_status->pcent : 0 ;


				$this->data->percent_value = $percent_value;
				$this->data->order_workflows = $this->workflows_m->form_select( [] , false );
				return $this->data;
		}
		else
		{
			throw new OrderNotFoundException('No valid User',100);
		}

	}


	private function no_ship()
	{
		$data = new ViewObject();

		$data->id = 0;
		$data->title = 'Shipping not required.';
		$data->slug = 'nsr';
		$data->module_type = 'shipping';
		$data->link = 'Shipping not required.';
		return $data;
	}

	public function _getAddressObject($id=0)
	{

		if($a = $this->addresses_m->get($id))
		{
			return $a;
		}

		return (object) [
			'id'=>0,
			'first_name'=>'',
			'last_name'=>'',
			'billing'=>'1',
			'address1'=>'',
			'address2'=>'',
			'phone'=>'',	
			'state'=>'',
			'zip'=>'',
			'country'=>'',	
			'email'=>'',
			'shipping'=>'1',
			'created'=>'',		
			'updated'=>'',		
			'deleted'=>'',		
			'company'=>'',	
			'instruction'=>'',	
			'user_id'=>'',
			'created_by'=>'',								
			];
	}


	private function get_order_items($order_id)
	{
		$items = $this->orders_admin_m->get_order_items($order_id);

		return $this->format_items( $items );
	}

	/**
	 * format order->items for display
	 */
	private function format_items($contents)
	{
		//var_dump($contents);die;
		foreach($contents as $key => $item)
		{

			$input = (array) json_decode($item->options);

			$str = '<br/>';
			foreach ($input as $key2 => $value)
			{
				$str .= $key2 . ':' . $value . '<br/>';
			}

			$contents[$key]->options = $str;

		}
		return $contents;
	}


	public function _getCheckoutMethodData($type='shipping',$id=0)
	{

		if($m = $this->db->where('module_type',$type)->where('id', $id)->get('nct_checkout_options')->row() )
		{
			$type = ($type=='gateway')?'gateways':'shipping';
			$m->link = "<a href='./".NC_ADMIN_ROUTE."/{$type}/edit/{$id}' title='Click to view {$m->title}' class='tooltip-s nc_links'>{$m->title}</a>";
			return $m;
		}

		return (object) ['id'=>0,'slug'=>'','title'=>'Not Found','link'=>'Not Found'];
	}

}
// END Cart Class