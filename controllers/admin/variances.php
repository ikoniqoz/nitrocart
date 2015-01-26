<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Variances extends Admin_Controller
{

	protected $section = 'products';


	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');


		$this->lang->load('nitrocart/nitrocart_admin_products');
		$this->load->model('nitrocart/zones_m');
		$this->load->model('nitrocart/admin/packages_groups_admin_m');
		$this->load->model('nitrocart/admin/products_variances_admin_m');

		$this->data = new ViewObject();
		$this->data->returnObject = $this->getAjaxReturnObject();

        $this->template
					->enable_parser(true)           
        			->set('base_amount_pricing',NC_BASEPRICING)
        			->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}


	//What do we do/or show here ?
	public function index()
	{
		return NULL;
	}


	/**
	 * This is executed when the user clicks 'Add Variance'
	 * This also handles the post back action
	 */
	public function create($product_id)
	{

        // Check permissions
		role_or_die('nitrocart', 'admin_r_catalogue_edit');

		//save data if postback
		if($input = $this->input->post())
		{
			$returnArray = $this->products_variances_admin_m->create( $product_id , $input );
			$this->sendAjaxReturnObject($returnArray);die;
		}

		//continue to display
		$this->data->available_groups = $this->packages_groups_admin_m->get_for_admin();

		//we need to get a list of shipping zones
		$this->load->model('nitrocart/zones_m');
		$this->data->shipping_zones = $this->zones_m->get_for_admin();		

		//get the default shpping zone id
		$row = $this->db->where('default',1)->get('nct_zones')->row();
		
		$def_id = ($row)?$row->id:0;
			

		$this->data->default_id = $def_id;


		$this->template
				->set_layout(false)
				->set('jsexec', "$('.tooltip-s').tipsy()")
				->set('available_groups',$this->data->available_groups)
				->build('admin/products/modals/add_variance',$this->data);
	}




	public function edit($variance_id)
	{

        // Check permissions
		role_or_die('nitrocart', 'admin_r_catalogue_edit');

		if($input = $this->input->post())
		{
			$returnArray = $this->products_variances_admin_m->edit( $input['product_id'], $variance_id , $input );
			$this->sendAjaxReturnObject($returnArray);die;
		}


		//we need to get a list of packages
		$this->data->available_groups = $this->packages_groups_admin_m->get_for_admin();

		//we need to get a list of shipping zones
		$this->data->shipping_zones = $this->zones_m->get_for_admin();

		$variance = $this->products_variances_admin_m->get($variance_id);


		$def_id = 0;
		//get the default shpping zone id
		if($row = $this->db->where('default',1)->get('nct_zones')->row())
		{
			$def_id = $row->id;
		}	
		$this->data->default_id = $def_id;

		$this->template
				->set_layout(false)
				->set('jsexec', "$('.tooltip-s').tipsy()")
				->set('available_groups',$this->data->available_groups)
				->set('variance',$variance)
				->build('admin/products/modals/edit_variance',$this->data);
	}




	public function shipping($action='get', $variance_id)
	{
        // Check permissions
		role_or_die('nitrocart', 'admin_r_catalogue_edit');


		if($action=='get')
		{
			//we need to get a list of packages
			$this->data->available_groups = $this->packages_groups_admin_m->get_for_admin();
			$variance = $this->products_variances_admin_m->get($variance_id);

			//we need to get a list of shipping zones
			$this->data->shipping_zones = $this->zones_m->get_for_admin();			

			$this->template
					->set_layout(false)
					->set('jsexec', "$('.tooltip-s').tipsy()")
					->set('available_groups',$this->data->available_groups)
					->set('variance',$variance)
					->build('admin/products/modals/edit_shipping',$this->data);
		}
		else
		{

			$returnArray = $this->products_variances_admin_m->edit_shipping( $variance_id , $this->input->post() );

			$this->sendAjaxReturnObject($returnArray);
		
		}
	}





	public function toggle_value( $variance_id )
	{

		$returnArray = $this->getAjaxReturnObject();
		$returnArray['message'] = 'Failed to edit variance';

		if($func = $this->input->post('func'))
		{
			$returnArray = $this->products_variances_admin_m->$func( $variance_id  );
		}

		$this->sendAjaxReturnObject($returnArray);
	}




	public function price($action='get', $variance_id)
	{

		switch ($action) 
		{
			case 'update':
				$this->data->returnObject['message'] = 'Failed to edit price';

				$returnArray = $this->getAjaxReturnObject();

				if($input = $this->input->post()  AND group_has_role('nitrocart', 'admin_r_catalogue_edit') )
				{

					if( isset($input['discountable']) AND  
						isset($input['price']) AND  
						isset($input['base']) AND 
						isset($input['rrp']) )
						{
							$returnArray = $this->products_variances_admin_m->edit_price( $variance_id , $input );
						} 
				}
				$this->sendAjaxReturnObject($returnArray);
				break;

			case 'get':
			default:

				$variance = $this->products_variances_admin_m->get($variance_id);

				$this->template
						->set_layout(false)
						->set('jsexec', "$('.tooltip-s').tipsy()")
						->set('variance',$variance)
						->build('admin/products/modals/edit_price');
				break;
		}
	}

	/**
	 * ajax verion of duplicate();
	 */
	public function duplicate_aj($variance_id)
	{
		$returnArray = $this->getAjaxReturnObject();

		if($returnArray = $this->products_variances_admin_m->duplicate_self( $variance_id  ))
		{
			//$this->sendAjaxReturnObject($returnArray);

			//will send as variable is updated
		}
	
		$this->sendAjaxReturnObject($returnArray);
	}

	/**
	 * @deprecated
	 */
	public function duplicate($product_id,$variance_id)
	{
		$returnArray = $this->products_variances_admin_m->duplicate_self( $variance_id  );
		$this->session->set_flashdata($returnArray['status'],$returnArray['message']);	
		redirect(NC_ADMIN_ROUTE.'/product/edit/'.$product_id.'#price-tab');
	}





	public function delete($row_id)
	{
		$array = [];	

		$array['status'] = JSONStatus::Error;
		$array['message'] = 'Failed to delete variance.';

		if( group_has_role('nitrocart', 'admin_r_catalogue_edit'))
		{
			if($this->products_variances_admin_m->delete($row_id))
			{
				$array['status'] = JSONStatus::Success;
				$array['message'] = '';		
			}
		}

		$this->sendAjaxReturnObject($array);		
	}


	private function getAjaxReturnObject()
	{
		$ret_array = [];
		$ret_array['status'] = JSONStatus::Error;
		$ret_array['message'] = '';
		return $ret_array;
	}

	private function sendAjaxReturnObject($array)
	{
		echo json_encode($array);die;
	}
}