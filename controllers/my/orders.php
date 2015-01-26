<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/mybase_controller.php');
class Orders extends MyBase_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('nitrocart/orders_m');
		$this->template
			->set_breadcrumb('Home', '/')
			->set_breadcrumb( Settings::get('shop_name') , '/'.NC_ROUTE)
			->set_breadcrumb('My', '/'.NC_ROUTE.'/my');
	}


	/**
	 *
	 *
	 * This will display a list of orders the customer
	 * has placed with the shop.
	 *
	 */
	public function index()
	{

		$data = new ViewObject();

		$data->items = $this->orders_m->order_by('id','desc')->get_all_by_user($this->current_user->id);

		// Display the page
		$this->template
			->set_breadcrumb('Orders')
			->title( Settings::get('shop_name'), 'MY Orders')
			->build('nitrocart/my/orders', $data);
	}


	/**
	 *
	 *
	 * Show the main dashboard menu and also display some usefull summary information about
	 * their transactions ect.
	 * field invoice is deprecated
	 */
	public function order($id)
	{
		$data = new ViewObject();

		$this->load->model('nitrocart/addresses_m');

		// Retrieve the order
		$data->order = $this->orders_m->where('user_id', $this->current_user->id)->get($id);
		if (!$data->order )
		{
			$this->session->set_flashdata('error', lang('nitrocart:my:order_not_found'));
			redirect( NC_ROUTE.'/my/orders');
		}

		$data->shipping = $this->addresses_m->get($data->order->shipping_address_id);
		$data->billing = $data->invoice = $this->addresses_m->get($data->order->billing_address_id);
		$data->transactions = $this->db->where('order_id', $id)->get('nct_transactions')->result();
		$data->contents = $this->orders_m->get_order_items($data->order->id);

		$this->template
				->set_breadcrumb('Orders',NC_ROUTE.'/my/orders')
				->set_breadcrumb("Order [{$id}]")
				->title(Settings::get('shop_name'),'MY Order')
				->build('nitrocart/my/order', $data);
	}


}