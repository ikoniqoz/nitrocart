<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Orders_m extends MY_Model
{

    public $_table = 'nct_orders';

    /**
     * Initiaize libraies and settings
     */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('nitrocart/Toolbox/Nc_string');
	}


	private function _order_defaults()
	{
		$order_defaults								= [];
		$order_defaults['contents'] 				= [];		
		$order_defaults['invoice'] 					= [];		
		$order_defaults['date_placed'] 				= date("Y-m-d H:i:s");
		$order_defaults['user_id'] 					= $this->session->userdata('user_id');

		return $order_defaults;
	}

	/**
	 * gets the first order workflow item
	 * @return [type] [description]
	 */
	private function _order_workflow()
	{
		// Get the status object (id and name)
		$status = $this->db->order_by('pcent','asc')->where('is_placed',1)->get('nct_workflows')->row();
		if(!$status)
		{
			$status = $this->db->order_by('pcent','asc')->get('nct_workflows')->row();
			if(!$status)
			{
				$status->id   = 0;
				$status->name = 'Not set.';
			}
		}
		return $status;
	}






	/**
	 * Create an order 
	 *
	 * Step 1: Place the order
	 * Step 2: Calculate Order items and place order_lines
	 * Step 3: Update Order with totals (ex tax)
	 *
	 *
	 * 
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	public function create($input)
	{
		/**
		 * 
		 */
		$this->load->model('nitrocart/tax_m');
		$this->load->helper('nitrocart/nitrocart_admin');
		$this->load->library('nitrocart/shipping2_library');




		/**
		 * get the order default values
		 * @var [type]
		 */
		$order_defaults 		= $this->_order_defaults();



		/**
		 * Create the main order line
		 */
		$order_defaults['order_id'] = $this->_create( $input , $order_defaults  );



		//grand totals
		$order_defaults['grand_total_points'] = $order_defaults['grand_total_shipping'] = $order_defaults['grand_total_discounts'] = $order_defaults['grand_total_subtotals'] =  $order_defaults['grand_total_tax'] = $order_defaults['grand_total_totals'] = 0;


		/**
		 * Calc each item and update
		 */
		foreach ($input['cart_items'] as $item)
		{
			//array data for the line
			$line 					= []; 
			$line['tax'] 			= $this->tax_m->calc( $item );

			/*
			pre-clean
			 *
			$line['tax']['inc_total'] = round($line['tax']['inc_total'],2);
			$line['tax']['exc_total'] = round($line['tax']['exc_total'],2);
			$line['tax']['total'] = round($line['tax']['total'],2);
			*/

			$line['discount'] 		= ($item['qty'] * $item['discount']);
			$line['subtotal'] 		= ((( $item['qty'] * $item['price'])) + $item['base'] ) - ($line['discount']  + $line['tax']['inc_total']);
			$line['total'] 			= ((( $item['qty'] * $item['price'])) + $item['base'] ) - ($line['discount']) + $line['tax']['exc_total'];


			/*post-clean
			$line['total'] = round($line['total'],2);
			$line['subtotal'] = round($line['subtotal'],2);
			$line['discount'] = round($line['discount'],2);
			$line['tax']['inc_total'] = round($line['tax']['inc_total'],2);
			$line['tax']['exc_total'] = round($line['tax']['exc_total'],2);
			*/
			$order_defaults['contents'][] 				= $this->_buildOrderLineItems( $order_defaults, $item, $line );
			$order_defaults['invoice'][]  				= $this->_buildInvoiceLineItems( $order_defaults, $item, $line );


			//accum grand totals
			$order_defaults['grand_total_discounts'] += round($item['discount'],2);
			$order_defaults['grand_total_subtotals'] += round($line['subtotal'],2);  
			$order_defaults['grand_total_tax'] 		 += round($line['tax']['total'],2); 
			$order_defaults['grand_total_totals'] 	 += round($line['total'],2);
			$order_defaults['grand_total_points'] 	 += ($item['points']*$item['qty']);

		}

		//var_dump($order_defaults);die;
		// Check for shipping tax row
		// Not required if no shipping is 
		// included or a digital product
		//if( ($input['shipping_id'] > 0) AND ( $input['has_shipping_address'] > 0) )
		$order_defaults['invoice'][] = $this->_buildShippingInvoiceLineItems( $order_defaults, $input );

		//update order totals
		// Update the order with the new calculated totals
		$oud = [];
		$oud['total_shipping'] 	= $input['cost_shipping'] ;			
		$oud['total_tax'] 		= $order_defaults['grand_total_tax'] ;		
		$oud['total_totals'] 	= $order_defaults['grand_total_totals'] ;	
		$oud['total_points'] 	= $order_defaults['grand_total_points'] ;
		$oud['total_discount'] 	= $order_defaults['grand_total_discounts'] ;
		$oud['total_subtotal'] 	= $order_defaults['grand_total_subtotals']  ;
		$result = $this->db->where('id',$order_defaults['order_id'])->update('nct_orders',$oud);


		//var_dump( $order_defaults['contents']);die;
		// insert data
		$this->db->insert_batch('nct_order_items', $order_defaults['contents'] );
		$this->db->insert_batch('nct_order_invoice', $order_defaults['invoice'] );


		return $order_defaults['order_id'];
	}

	/**
	 * Create the main order line
	 * @param  [type] $order    [description]
	 * @param  [type] $defaults [description]
	 * @return [type]           [description]
	 */
	private function _create($order,$defaults)
	{

		// Get the default workflow status 
		$status = $this->_order_workflow();

		$i_status = $this->db->insert('nct_orders', array(
				'user_id' 					=> $defaults['user_id'],	
				'shipping_id' 				=> $order['shipping_id'],
				'gateway_id' 				=> $order['gateway_method_id'],
				'billing_address_id' 		=> $order['billing_address_id'],
				'shipping_address_id' 		=> $order['shipping_address_id'],
				'has_shipping_address' 		=> (int) $order['has_shipping_address'],				
				'session_id' 				=> $order['session_id'],
				'ip_address' 				=> $this->input->ip_address(),
				'status_id'					=> $status->id, 
				'status'					=> $status->name, 
				'order_date' 				=> time(),
				'paid_date' 				=> NULL, 
				'total_tax' 				=> 0, 
				'total_shipping' 			=> 0, //$order['cost_shipping'],
				'total_discount' 			=> 0, //$order['cost_shipping'],
				'total_subtotal' 			=> 0,//$order['order_total'],
				'total_totals'				=> 0,
				'total_points'				=> 0,
				'count_items' 				=> count($order['cart_items']),
                'created_by'    			=> $defaults['user_id'],
                'created'       			=> $defaults['date_placed'],
                'updated'       			=> $defaults['date_placed'],				
		));
		
		return ($i_status)?$this->db->insert_id():false;		
	}


	private function _buildOrderLineItems($order_defaults,$item,$line)
	{
		$line_item =[
			'order_id' 				=> $order_defaults['order_id'],
			'product_id' 			=> $item['productid'],
			'variant_id' 			=> $item['id'],
			'title' 				=> $item['name'],
			'qty' 					=> $item['qty'],
			'options' 				=> json_encode($item['options']),					
	    ];	

		return $line_item;
	}

	/**
	 * 
	 * @param  [type] $order_defaults [description]
	 * @param  [type] $item           [description]
	 * @param  [type] $line           [description]
	 * @return [type]                 [description]
	 */
	private function _buildInvoiceLineItems($order_defaults,$item,$line)
	{	
		$invoice = [
				'order_id' 			=> $order_defaults['order_id'],
				'title' 			=> $item['name'] . '  &bull; '. nc_variant_name($item['id']) , 
				'product_id' 		=> $item['productid'],
				'variant_id' 		=> (int) $item['id'],				
				'qty' 				=> $item['qty'],					
				'price' 			=> $item['price'], 
				'base' 				=> $item['base'],
				'tax'				=> $line['tax']['total'], 
				'tax_rate'			=> $line['tax']['rate'],				
				'discount'			=> $item['discount'],
				'discount_message'	=> $item['discount_message'],
				'discount'			=> round($line['discount'],2),
				'subtotal' 			=> round($line['subtotal'],2), 
				'total' 			=> round($line['total'],2), 
	            'created'       	=> $order_defaults['date_placed'],
	            'updated'       	=> $order_defaults['date_placed'],		
	            'created_by'		=> $order_defaults['user_id'],					
		];

		return $invoice;		
	}

	/**
	 * Shipping is always tax inclusive
	 * So we reverse the subtotal from total-tax
	 * @param  [type]  $order_id      [description]
	 * @param  [type]  $total         [description]
	 * @param  integer $tax_inc_amout [description]
	 * @return [type]                 [description]
	 */
	private function _buildShippingInvoiceLineItems( $order_defaults, $input )
	{

		//total and subtotal have the same
		$invoice = [
				'order_id' 			=> $order_defaults['order_id'],
				'title' 			=> 'Shipping and Handling',

				'product_id' 		=> NULL,
				'variant_id' 		=> NULL,				
				'qty' 				=> 1,					
				'price' 			=> $input['cost_shipping'], 
				'base' 				=> 0,
				'tax'				=> $input['shipping_tax'], 
				'tax_rate'			=> 0,	
				'discount'			=> 0,							
				'discount_message'	=> '',
				'subtotal' 			=> (floatval($input['cost_shipping']) - floatval($input['shipping_tax']) ), 
				'total' 			=> $input['cost_shipping'], 
	            'created'       	=> $order_defaults['date_placed'],
	            'updated'       	=> $order_defaults['date_placed'],		
	            'created_by'		=> $order_defaults['user_id'],					
		];

		return $invoice;
	}


	public function store_order_params($id, $data_array=[] )
	{
		//prepare the data
		$data = json_encode($data_array);
		$update_data = [
			'data' => $data
		];
		$result = $this->update($id, $update_data);
		return $result;
	}


	public function set_status($id, $status_workflow_row)
	{
		$update_info = array(
				'status_id' => $status_workflow_row->id,
				'status' => $status_workflow_row->name
		);
		$result = $this->update($id, $update_info);
		return $result;
	}

	public function mark_as_paid($id)
	{
		// first get the order for validity
		if($order = $this->get($id))
		{
			if( $order->paid_date == NULL )
			{
				//update order info
				$update_info = array(
						'paid_date' => now(),
				);

				if($result = $this->update($id, $update_info))
				{
					//update credit
					if($order->user_id > 0)
					{
						if($customer = $this->db->where('user_id',$order->user_id)->get('nct_customers')->row())
						{
							$int_credit = (int) $customer->store_credit + (int) $order->total_points;
							$this->db->where('user_id',$order->user_id)->update('nct_customers', ['store_credit' => $int_credit ] );
							//log a txn that credit is being applied too!
						}
					}
				}


				return $result;	
			}

		}

	}

	public function get_all_by_user($user_id)
	{
		$this->db->where('user_id',$user_id);
		return parent::get_all();
	}


	public function get_last($limit = 5)
	{
		return $this->limit($limit)->get_all();
	}


	/**
	 * Get All items in Order
	 * @param INT $id Order ID
	 * @old Set to true for admin, the admin collects all product data for display
	 */
	public function get_order_items($id)
	{
		return $this->db
			->select('nct_products.*, nct_order_items.*, nct_products.id as `id`')
			->join('nct_products', 'nct_order_items.product_id = nct_products.id','right')
			->where('order_id', $id)->get('nct_order_items')->result();
	}

	/**
	 * If a product exist in table then return true
	 * @param INT $id Order ID
	 */
	public function has_item($id)
	{
		$items =  $this->db->select('product_id')->where('product_id', $id)->limit(1)->get('nct_order_items')->row();
		return (count($items) > 0)? true : false ;
	}


	/**
	 * Delete an order is not a normal thing to do,
	 * we need to ask confirmation first.
	 * @return [type] [description]
	 * Do not let people delete orders, find a way to make this secure but have this functionaility for super admins or something
	 */
	public function delete($order_id)
	{
		return false;
	}


}