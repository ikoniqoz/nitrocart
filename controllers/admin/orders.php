<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

use Nitrocart\Exceptions\OrderNotFoundException as OrderNotFoundException;

class Orders extends Admin_Controller
{

	// Define Section
	protected $section = 'orders';
	private $data;

	/**
	 * @constructor
	 */
	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		role_or_die('nitrocart', 'admin_orders');


		$this->load->model('nitrocart/addresses_m');
		$this->load->library('nitrocart/orders_library');
		$this->lang->load('nitrocart/nitrocart_admin_orders');

		$this->mod_path = base_url() . $this->module_details['path'];

		$this->config->load('nitrocart/admin/'.NC_CONFIG);		
        $this->can_delete_orders = $this->config->item('admin/delete_orders');
     
        $this->data->base_amount_pricing = NC_BASEPRICING;
  
        $this->template
        			->set('admin_can_delete',$this->can_delete_orders)
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_js('nitrocart::admin/util.js')
                    ->append_js('nitrocart::admin/orders.js')
                    ->append_js('nitrocart::admin/common.js')
                    ->append_css('nitrocart::admin/stags.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/admin.css')
					->append_metadata('<script type="text/javascript">' . "\n  var MOD_PATH = '" . $this->mod_path . "';" . "\n</script>")
					->append_js('nitrocart::admin/orders.js')
					->set('base_amount_pricing',$this->data->base_amount_pricing);	
	}

	/**
	 * @description This is the Default list view page of Orders
	 * $index refers to the pagination index, 0 as default is get from first , but we can pass 5 and orders from 5-> will appeaer
	 */
	public function index( $offset = 0 )
	{
		$data   = new ViewObject();
		$filter = $this->prepFilters($data);

		$data->filters = new ViewObject();
		$data->filters->modules = [];
		$data->filters->modules['nitrocart,all'] = 'All Orders';

		$this->load->model( $data->namespace );
		$total_rows = $this->orders_admin_m->filter_count($filter);
		$data->pagination = create_pagination( NC_ADMIN_ROUTE . '/orders/callback', $total_rows, $data->limit, 5);						
		$data->orders =  $this->orders_admin_m->filter($filter , $data->pagination['limit'] , $data->pagination['offset']);	

		Events::trigger('SHOPEVT_AdminOrdersListGetFilters', $data->filters);	

		$this->load->model('nitrocart/workflows_m');
		$order_workflows = $this->workflows_m->form_select( array('all'=>lang('global:select-all')) , false );

		// Build the view with NC_ROUTE/views/admin/products.php
		$this->template->title($this->module_details['name'])
				->enable_parser(true)
				->append_js('admin/filter.js')
				->append_js('nitrocart::admin/orders.js')
				->set('order_workflows',$order_workflows)				
				->build('admin/orders/orders', $data);			
	}

	public function callback($offset = 0)
	{
		$data = new ViewObject();
		$filter = $this->prepFilters($data,true);

		$this->load->model( $data->namespace );
		$total_rows = $this->orders_admin_m->filter_count($filter);
		$data->pagination = create_pagination( NC_ADMIN_ROUTE. '/orders/callback', $total_rows, $data->limit, 5);						
		$data->orders =  $this->orders_admin_m->filter($filter , $data->pagination['limit'] , $data->pagination['offset']);	

		// set the layout to false and load the view
		$this->template
				->set_layout(false)
				->enable_parser(true)
				->set('pagination', $data->pagination)
				->build('admin/orders/line_item',$data);

	}

	private function prepFilters(&$data, $preSave=false,$filter=array())
	{
		$filter['status']  = $data->f_status = $this->_get_filter_setting( 'f_status', 'display_orders_f_status_filter' , 'active',$preSave);
		$filter['f_keyword_search']  = $data->f_keyword_search = $this->_get_filter_setting( 'f_keyword_search', 'display_orders_f_keyword_search_filter' , '',$preSave);
		$filter['f_order_by']  = $data->f_order_by = $this->_get_filter_setting( 'f_order_by', 'display_orders_f_order_by_filter' , 'id',$preSave);
		$filter['f_order_by_dir']  = $data->f_order_by_dir = $this->_get_filter_setting( 'f_order_by_dir', 'display_orders_f_order_by_dir_filter' , 'desc',$preSave);
		$filter['f_display_count']  = $data->limit = $data->f_display_count = $this->_get_filter_setting( 'f_display_count', 'display_f_display_count_filter' , 5,$preSave);
		$filter['f_payment_status']  = $data->f_payment_status = $this->_get_filter_setting( 'f_payment_status', 'display_f_payment_status_filter' , 'all' , $preSave);
		$filter['f_order_status']  = $data->f_order_status = $this->_get_filter_setting( 'f_order_status', 'display_f_order_status_filter' , 'all' , $preSave);

		//interop filter
		$filter_values = $this->_get_filter_setting( 'f_filter', 'display_f_filter_status_filter' , 'nitrocart,all' , $preSave);
		$filter_values = explode(',' , $filter_values);
		$data->namespace = trim( $filter_values[0] ) . '/admin/orders_admin_m'; 
		$filter['f_filter']  = trim($filter_values[1]);

		return $filter;
	}


 	private function _get_filter_setting($a='',$b,$c=0,$d=false){if($d){$e=$this->input->post($a);$this->session->set_userdata($b,$e);return $e;}if($this->input->post($a)){$c=$this->input->post($a);if($this->session->userdata($b)!=$this->input->post($a)){$this->session->set_userdata($b,$c);}}else{$c=($this->session->userdata($b))?$this->session->userdata($b):$c;}return $c;}
	


	/**
	 * Create a new order (Backend)
	 */
	public function create()
	{
		redirect( NC_ROUTE );
	}

	/**
	 * Admin access to View an order placed by customer
	 * @param unknown_type $id
	 *
	 * Musch of this needs to be moved to a lib
	 */
	public function order($id,$set_layout = true)
	{
		$this->load->model('nitrocart/admin/orders_admin_m');		


		try
		{
			$order = $this->orders_library->get_admin_order($id);
		}
		catch(OrderNotFoundException $e)
		{
			$this->session->set_flashdata(JSONStatus::Error, $e->getMessage());
			redirect(NC_ADMIN_ROUTE.'/orders');		
		}
		

		if(!$set_layout)
			$this->template->set_layout(false);

		// Build Output
		$this->template
			->title($this->module_details['name'])
			->set('show_actions',$set_layout)
			->set('user', $this->current_user)
			->enable_parser(true)
			->build('admin/orders/order', $order);							
	}


	/**
	 * mapaid = Marked As Paid (by admin)
	 *
	 */
	public function mapaid($order_id)
	{
		$this->load->model('nitrocart/admin/orders_admin_m');	

		$this->orders_admin_m->mark_as_paid($order_id);

		$this->orders_admin_m->create_note($order_id, $this->current_user->id,
				$this->current_user->username.' has manually marked this order as <span class="s_status s_paid">PAID</span>'
			);

		redirect(NC_ADMIN_ROUTE.'/orders/order/'.$order_id.'#actions-tab');
	}


	public function setstatus($id = 0,$from_status=0)
	{
		$this->load->model('nitrocart/admin/orders_admin_m');	
		$this->load->model('nitrocart/workflows_m');		

		//get the new status
		$new_status = $this->input->post('order_status');

		//Can not be the same status
		($new_status != $from_status) OR redirect(NC_ADMIN_ROUTE.'/orders/order/'.$id);

		$from_status = $this->workflows_m->get($new_status);

		if( $status = $this->workflows_m->get($new_status) )
		{
			// Set the status
			$result = $this->orders_admin_m->set_status($id, $status);
			$this->orders_admin_m->create_note($id,$this->current_user->id, sprintf('<br>changed status from <span class="stags green">%s</span> to <span class="stags blue">%s</span>',$from_status->name,$status->name));
			
		}

		redirect(NC_ADMIN_ROUTE.'/orders/order/'.$id);
	}


	//notifies system to resend an invoice email.
	//this action can only be taken out by the admin viewing an order
	public function reinvoice($id)
	{
		Events::trigger('SHOPEVT_SendOrderInvoice',$id);
		redirect(NC_ADMIN_ROUTE.'/orders/order/'.$id.'#actions-tab');
	}


	public function notes()
	{
		$this->load->model('nitrocart/admin/orders_admin_m');			
		if ($this->input->post('order_id'))
		{
			$order_id = $this->input->post('order_id');

			$message = $this->input->post('message');

			if($order_id && $this->orders_admin_m->create_note($order_id, $this->current_user->id ,$message))
			{
				$this->session->set_flashdata(JSONStatus::Success, lang('success'));
			}
			else
			{
				$this->session->set_flashdata(JSONStatus::Error, lang('error'));
			}
		}

		redirect( NC_ADMIN_ROUTE. '/orders/order/'.$order_id);
	}


	public function viewtx($txn_id = 0)
	{
		$this->load->model('nitrocart/admin/orders_admin_m');	
				
		/*if from post*/
		if ($this->input->post())
		{
			$input = $this->input->post();
			$txn_id = $input['txn_id'];
		}

		$arr = [];

		// replace this with transaction details
		$tdata = $this->db->where('id',$txn_id)->get('nct_transactions')->row();


		$arr['status'] = 'Retrieved Transaction Details @ ' . date("H:M:s d-M-Y");
		$arr['message'] = '';
		$arr['user'] = $tdata->user;
		$arr['id'] = $tdata->id;
		$arr['order_id'] = $tdata->order_id;
		$arr['txn_id'] = $tdata->txn_id;
		$arr['txn_status'] = $tdata->status;
		$arr['reason'] = $tdata->reason;
		$arr['amount'] = $tdata->amount;
		$arr['refund'] = $tdata->refund;
		$arr['timestamp'] = $tdata->timestamp;

		$arr['data'] = [];


		// Build Output
		$this->template
			->set_layout(false)
			->enable_parser(true)
			->build('admin/orders/modal/txn', $arr);
	}

	public function delete($id,$key='')
	{
		if($this->can_delete_orders)
		{
			$this->load->model('nitrocart/admin/orders_admin_m');			
			$this->orders_admin_m->delete($id);
			$this->session->set_flashdata(JSONStatus::Success,'Order has been deleted.');
		}
		else
		{
			$this->session->set_flashdata(JSONStatus::Error,'Order cannot be deleted.');		
		}
		redirect(NC_ADMIN_ROUTE.'/orders');
	}

	public function filter($action='clear')
	{
		$this->orders_library->clear_admin_orders_filter();
		redirect(NC_ADMIN_ROUTE.'/orders');
	}	


}