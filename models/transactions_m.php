<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

/*
 * This file needs more attention
 *
 */
class Transactions_m extends MY_Model
{

    public $_table = 'nct_transactions';

	public function __construct()
	{
		parent::__construct();
	}

	public function create($input)
	{
		$input['timestamp'] = time();
		return $this->insert($input);
	}

	/**
	 * used once in checkout_core
	 * 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function log_new_order($id)
	{
		return $this->log($id, 0,  0 ,'CUSTOMER', 'Order Placed', 2);
	}





	/**
	 * log($id, $credit,  $refund ,$user = 'SYSTEM', $message = '',$status=2)
	 *
	 *
	 * @param INT $id Order ID
	 * @param DEC $credit Amount to credit the Store
	 * @param DEC $refund Amount to refund to customer
	 * @param String $user User name - not the Actual usename but the scope - SYSTEM/ADMIN or CUSTOMER - Could also be a Payment Gateway
	 * @param String $message Message to record in System
	 * @param INT $status Status Level to record (Pending, Refected or Accepted) 0/1/2
	 *
	 * @return INT The ID of the record created
	 *
	 */
	public function log($id, $credit=0,  $refund=0 ,$user = 'SYSTEM', $message = '',$status='accepted', $data =[])
	{

		$to_insert = [
				'order_id' => $id,
				'txn_id' => $id,
				'status' => $status,
				'reason' => $message,
				'refund' => $refund,
				'amount' => $credit,
				'gateway' => 0,
				'user' => $user,
				'timestamp' => time(),
				'data' => json_encode($data),
		];

		return $this->create($to_insert); //returns id
	}



	/**
	 * [gateway_cancel description]
	 * @param  [type] $order_id [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public function gateway_cancel($order_id, $data=NULL )
	{    
		return $this->merchant_response($order_id, 0, 'User Cancelled', 0, 0, 'Cancelled', $data ); //returns id
	}


	/**
	 * Log the response from the merchant
	 * 
	 * @param  [type] $order_id [description]
	 * @param  [type] $credit   [description]
	 * @param  [type] $reason   [description]
	 * @param  [type] $refund   [description]
	 * @param  [type] $gateway  [description]
	 * @param  [type] $status   [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public function merchant_response($order_id, $credit, $reason, $refund, $gateway, $status, $data )
	{

		$to_insert = [
				'order_id' 	=> $order_id,
				'txn_id' 	=> $order_id,
				'status' 	=> $status,
				'reason' 	=> $reason,
				'refund' 	=> $refund,
				'amount' 	=> $credit,
				'gateway' 	=> $gateway->title,
				'user' 		=>  $this->current_user->id,
				'data' 		=> json_encode($data),
	            'created'   => date("Y-m-d H:i:s"),
	            'updated'   => date("Y-m-d H:i:s"),	
	            'created_by'=> $this->current_user->id,			
				'timestamp' => time(),
		];

		return $this->create($to_insert); //returns id
	}
}