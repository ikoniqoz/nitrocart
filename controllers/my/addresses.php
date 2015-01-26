<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/mybase_controller.php');
class Addresses extends MyBase_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->load->model('nitrocart/addresses_m');
		$this->load->helper('nitrocart/nitrocart');		
		$this->lang->load('nitrocart/nitrocart');


		$this->address_validation = $this->addresses_m->address_validation ;

		$this->template
			->set_breadcrumb('Home', '/')
			->set_breadcrumb(Settings::get('shop_name'), '/'.NC_ROUTE)
			->set_breadcrumb('My', '/'.NC_ROUTE.'/my');

	}


	/**
	 *
	 *
	 * This will display a dashboard to the customer
	 * of the options they can do Essentially provide
	 * a list of links so they can modify their data
	 */
	public function index()
	{
		$data = new ViewObject();

		$data->items = $this->addresses_m->get_active_by_user($this->current_user->id);

		$this->template
			->set_breadcrumb('Addresses')
	  		->title(Settings::get('shop_name'))
			->build('nitrocart/my/addresses', $data);

	}

	public function create()
	{

		$this->data = new ViewObject();

		$this->data->user_id = $this->current_user->id;

		// Add new address
		if ($input = $this->input->post())
		{

			unset($input['submit']);
			$input['user_id'] = $this->current_user->id;

			$this->form_validation->set_rules($this->address_validation);
			$this->form_validation->set_rules('useragreement', 'User Agreement', 'required|numeric|trim');

			if ( $this->form_validation->run() )
			{
				$bil_a = 0;
				$ship_a = 0;
				//determin what type of address
				switch ($this->input->post('address_type'))
				{
					case 0:
						$ship_a = 1 ;
						break;
					case 1:
						$bil_a = 1;
						break;
					case 2:
						$bil_a = 1;
						$ship_a = 1 ;
						break;
				}

				$this->session->set_flashdata('success', lang('nitrocart:my:address_created_success'));
				$success = $this->addresses_m->create($input,$bil_a,$ship_a);
				redirect( NC_ROUTE .'/my/addresses');
			}

		}

		foreach ($this->address_validation as $item)
		{
			$this->data->{$item['field']} = '';
		}

		$this->template
			->set_breadcrumb('Addresses',NC_ROUTE.'/my/addresses')
			->set_breadcrumb('Create Address')
			->title(Settings::get('shop_name'))
			->build('nitrocart/my/create_address', $this->data);

	}


	public function delete($id)
	{
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/my/addresses';			
		
		$result = $this->addresses_m->remove($id , $this->current_user->id);
		if ($result) $this->session->set_flashdata('success', lang('nitrocart:my:address_deleted_success'));
		redirect($this->refer);
	}

}