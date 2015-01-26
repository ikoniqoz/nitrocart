<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Customers extends Admin_Controller
{

	// Define Section
	protected $section = 'customers';
	private $data;

	/**
	 * @constructor
	 */
	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		role_or_die('nitrocart', 'admin_customers');

        $this->lang->load('nitrocart/nitrocart_admin_customers');
        $this->load->library('nitrocart/Toolbox/Nc_status');
        
		$this->mod_path = base_url() . $this->module_details['path'];

        $this->template
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_js('nitrocart::admin/util.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css')
					->append_metadata('<script></script>')
					->append_metadata('<script type="text/javascript">' . "\n  var MOD_PATH = '" . $this->mod_path . "';" . "\n</script>")
					->append_js('nitrocart::admin/orders.js');
	}


	public function index()
	{
		$this->filter(0);
	}

	public function filter($offset=0)
	{

		if($offset=='clear') $this->clearFilter();
		
		$this->load->model('nitrocart/customers_m');

		$data = new ViewObject();
		$data->customers = [];

		$limit = 10;

		$data->str_search = $this->prepFilter();
	
		$total_items = $this->customers_m->filter_count($data->str_search);


        $data->pagination = create_pagination(NC_ADMIN_ROUTE.'/customers/filter', $total_items, $limit,5);		


		$data->customers =   $this->customers_m->filter($data->str_search,$data->pagination['limit'] , $data->pagination['offset']);

		$data->admin_id = $this->get_admin_group_id();


		$this->template->title($this->module_details['name'])
			->enable_parser(true)	
			->build('admin/customers/list', $data);			
	}

	public function orders($user_id=0)
	{
		$this->ordersfilter($user_id,0);
	}

	public function ordersfilter($user_id=0, $offset=0)
	{
		$data = new ViewObject();
		$limit = 5;

		$customer = $this->db->select('*')->where('user_id',$user_id)->get('profiles')->row();
        $total_items = $this->db->where('user_id',$user_id)->count_all_results('nct_orders');		
        $data->pagination = create_pagination(NC_ADMIN_ROUTE.'/customers/ordersfilter/'.$user_id.'/', $total_items, $limit,6);		
        $data->orders = $this->db->order_by('id','desc')->limit( $data->pagination['limit'] , $data->pagination['offset'] )->where('user_id',$user_id)->get('nct_orders')->result();    

		$this->template
				->set('customer',$customer)
				->enable_parser(true)
				->build('admin/customers/orders',$data);	
	}	


	public function addresses($user_id=0, $offset=0)
	{
		$data = new ViewObject();
		$limit = 5;

		if($customer = $this->db->select('*')->where('user_id',$user_id)->get('profiles')->row())
		{
	        $total_items = $this->db->where('user_id',$user_id)->count_all_results('nct_addresses');		
	        $data->pagination = create_pagination(NC_ADMIN_ROUTE.'/customers/addresses/'.$user_id.'/', $total_items, $limit,6);		
	        $data->addresses = $this->db->order_by('id','desc')->limit( $data->pagination['limit'] , $data->pagination['offset'] )->where('user_id',$user_id)->get('nct_addresses')->result();    

			$this->template
					->set('customer',$customer)
					->enable_parser(true)
					->build('admin/customers/addresses',$data);	
		}
		else
		{
			echo "oops..";
		}
	}


	public function edit($id = 0)
	{

		$status = $this->validateUserCustomerExistance($id);

		if($status->getStatus()===false)
		{
			$this->session->set_flashdata(JSONStatus::Error,$status->getMessage());
			redirect(NC_ADMIN_ROUTE.'/customers');
		}

		$myobj = new ViewObject();

		$this->load->driver('Streams');

		$extra['title'] = "Customer : " . user_displayname($id, false) . " | UserID: {$id} "; //name of user
		$extra['buttons'] = [];
		$extra['return'] = NC_ADMIN_ROUTE.'/customers';


		$hidden = []; 
		$defaults = [];

		//skips
		$skips = array('user_id' ,'default_billing_id' , 'default_shipping_id', 'first_name','last_name','total_orders','last_order' );

		$myobj->tabs = array(
		    [
		        'title'     => lang('nitrocart:customers:general_info'),
		        'id'        => 'general-tab',
		        'fields'    => array('signup_email', 'account_id','store_credit')
		    ],		    
		);


		// Send object to do a round of request of display
		Events::trigger('SHOPEVT_AdminCustomerData',$myobj);


		$row = $this->db->where('user_id', $id)->get('nct_customers')->row();
		$user = $this->db->where('user_id', $id)->get('profiles')->row();
	
		$tabbed_html = $this->streams->cp->entry_form('customers', 'nz_zones', 'edit', $row->id, false, $extra,$skips,$myobj->tabs,$hidden,$defaults);
		$this->template
				->enable_parser(true)
				->set('user',$user)
				->set('customer_id',$id)
				->set('tabbed_html', $tabbed_html)
				->build('admin/customers/edit');	
	}


	public function group($user_id)
	{
		//only if there is a post
		if($this->_handle_postback())
		{
			$this->session->set_flashdata(JSONStatus::Success,'Group Changed');
			redirect(NC_ADMIN_ROUTE.'/customers/group/'.$user_id);
		}

		is_numeric($user_id) OR redirect(NC_ADMIN_ROUTE.'/customers');

		$data = new ViewObject();
		$data->user = $this->get_user($user_id);


		if( $this->can_edit_user($data->user) == true)
		{
			
			$this->load->model('groups/group_m');

			//we are going to double check that admin isnt in list
			$params['except'] = 'admin';
			$admin_id = $this->get_admin_group_id();
			$data->pyroUserGroups = array_for_select($this->group_m->where('id !=',$admin_id)->get_all(),'id', 'description');
			$this->template
					->enable_parser(true)
					->build('admin/customers/group',$data);	

		}
		else
		{
			redirect(NC_ADMIN_ROUTE.'/customers');
		}
	}

	private function _handle_postback()
	{
		if($input = $this->input->post())
		{
			$user_id =  $input['user_id'];
			$new_group_id =  $input['group_id'];
			if($status = $this->db->where('id',$user_id)->update('users', array( 'group_id' => $new_group_id ) ) )
			{
				return true;
			}
		}
		return false;
	}


	public function can_edit_user($user)
	{
		$can_edit = true;
		if($user->group_id == $this->get_admin_group_id())
		{
			$can_edit = false;
		}
		return $can_edit;
	}

	private function get_user($user_id)
	{
		$this->load->library('users/ion_auth');
		$user = ci()->ion_auth->get_user($user_id);

		return $user;
	}


	private function get_admin_group_id()
	{
		if($row = $this->db->where('name','admin')->get('groups')->row())
		{
			return $row->id;
		}
		return 1;
	}


	/**
	 * Retyurns the status of the user/customer
	 * 
	 * 
	 * NCMessageObject()->getStatus() == true|false
	 * ->getMessage() == The ID and message of the response
	 */
	private function validateUserCustomerExistance($id)
	{
		//set the initial status to false just in case
		$status = new NCMessageObject(false);


		if($row = $this->db->where('user_id', $id)->get('profiles')->row())
		{
			if($row = $this->db->where('user_id', $id)->get('nct_customers')->row())
			{
				$status->setStatus(true);
			}
			else
			{
				$status->setMessage("No Customer File Found..");
			}
		}
		else
		{
			$status->setMessage("No message..");
			if($row = $this->db->where('user_id', $id)->get('nct_customers')->row())
			{
				$status->setMessage("There is no user with this ID on the system. It may have been removed.");
			}
			else
			{
				$status->setMessage("There is no trace of this user ever existing on the system.");
			}			
			
		}

		return $status;
	}


	private function prepFilter()
	{

		if( $str_search = $this->input->post('f_name') )
		{
			if( trim($str_search) == '' )
			{	
				$this->clearFilter();
				return '';
			}
			return $this->setFilter($str_search);
		}

		if($this->session->userdata('display_customer_f_name'))
		{
			return $this->session->userdata('display_customer_f_name');
		}

		return '';	
	}

	private function setFilter($val)
	{
		if(trim($val) != '')
		{
			$this->session->set_userdata('display_customer_f_name',$val);
			return $val;
		}

		$this->clearFilter();
		return '';
	}

	private function clearFilter()
	{
		$this->session->set_userdata('display_customer_f_name','');
		$this->session->unset_userdata('display_customer_f_name');
	}
}