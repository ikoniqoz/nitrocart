<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Shipping extends Admin_Controller
{

	protected $section = 'shipping';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		role_or_die('nitrocart', 'admin_checkout');

		$this->lang->load('nitrocart/nitrocart_admin_shipping');
		$this->load->library('nitrocart/shipping2_library');
		$this->load->library('form_validation');

        $this->template
        			->enable_parser(true)
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');

	}

	/**
	 * List all items
	 */
	public function index()
	{
		//NULL gives us both enabled and disabled
		$this->data->installed = $this->shipping2_library->get_all_installed(NULL);
		$this->data->uninstalled = $this->shipping2_library->get_all_available();
		$this->template
				->title($this->module_details['name'])
				->build('admin/shipping/options/items',$this->data);
	}

	public function edit($id)
	{


		// Get the shipping method 
		// This call will get both the DB 
		// and the driver and merge them
		$shipping_method = $this->shipping2_library->get_installed($id);		



		// 
		// What we do if we cant find it
		// 
		if( (! $shipping_method ) )
		{
			$this->session->set_flashdata(JSONStatus::Error,'Unable to locate Shipping Option');
			redirect(NC_ADMIN_ROUTE . '/shipping');
		}



		//  
		//  Set the validation rules
		//  
		$this->form_validation->set_rules($shipping_method->fields);


		//
		// Run the validation on a postback
		//
		if ( $input = $this->input->post() AND $this->form_validation->run() )
		{

			// sanitize
			$input = $shipping_method->pre_save($input);

			if ($this->shipping2_library->save( $input ) )
			{
				$this->session->set_flashdata(JSONStatus::Success, lang('global:success'));

				if($this->input->post('btnAction')=='save_exit')
				{
					redirect(NC_ADMIN_ROUTE.'/shipping/');
				}
				
				redirect(NC_ADMIN_ROUTE.'/shipping/edit/' . $id);
			}
			else
			{
				//error validating values
				$this->session->set_flashdata(JSONStatus::Error, 'An error occured..');
				redirect( NC_ADMIN_ROUTE  . '/shipping/edit/' . $id);
			}
		}


		// call just before 
		// display for initializations
		$shipping_method->pre_output();



		$this->template
					->title($this->module_details['name'], lang('create'))
					->build('admin/shipping/options/form', $shipping_method);
	}


	/**
	 * Install shipping method by slug. We dont have 
	 * an ID so we need to locate the method by the driver in the system
	 * 
	 * @param  [type] $slug [description]
	 * @return [type]       [description]
	 */
	public function install($slug)
	{
		if ($this->shipping2_library->install($slug))
		{
			$this->session->set_flashdata(JSONStatus::Success, lang('success'));
		}
		else
		{
			$this->session->set_flashdata(JSONStatus::Error, lang('error'));
		}

		redirect(NC_ADMIN_ROUTE . '/shipping/');
	}


	/**
	 * Uninstall does not Delete. We practice DND so it simply marks the
	 * method as deleted for future reference
	 * 
	 * @param  integer $id [description]
	 * @return [type]      [description]
	 */
	public function uninstall($id = 0)
	{
		if (is_numeric($id))
		{
			$result = $this->shipping2_library->uninstall($id);

			if (!$result)
			{
				$this->session->set_flashdata(JSONStatus::Error, lang('nitrocart:shipping:delete_error'));
			}
		}
		redirect(NC_ADMIN_ROUTE . '/shipping');
	}


	public function enable($id)
	{
		$this->shipping2_library->enable($id);
		redirect(NC_ADMIN_ROUTE . '/shipping/');
	}

	/**
	 * Disable the shipping method
	 * Wil keep it in the DB but will not enable 
	 * this for shoppers
	 * 
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function disable($id)
	{
		$this->shipping2_library->disable($id);
		redirect(NC_ADMIN_ROUTE.'/shipping/');
	}

}