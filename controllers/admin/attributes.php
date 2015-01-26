<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Attributes extends Admin_Controller
{

	protected $section = 'attributes';

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		//$this->input->is_ajax_request() OR redirect(NC_ADMIN_ROUTE);
		$this->lang->load('nitrocart/nitrocart_admin_products_types');		
		$this->load->model('nitrocart/admin/attributes_m');
		$this->load->library('nitrocart/eav_library');
		$this->data = new ViewObject();
	}


	public function ajax_get($product_id, $variance_id)
	{
		$this->load->model('nitrocart/e_attributes_m');
		$this->load->model('nitrocart/admin/products_variances_admin_m');
		$this->load->model('nitrocart/admin/products_admin_m');
		$this->data->variance = $this->products_variances_admin_m->get($variance_id);
		$this->data->forms = $this->e_attributes_m->get_by_variance_id($variance_id);
		$this->data->product = $this->products_admin_m->get($product_id);

		// Ajax response
		if($this->input->is_ajax_request()) $this->template->set_layout(false);		

		$has_attr = (count($this->data->forms))?true:false;
		
		$this->template
				->enable_parser(true)		
				->set('jsexec', "$('.tooltip-s').tipsy()")
				->set('has_attr', $has_attr)
				->build('admin/products/modals/edit_attributes',$this->data);
	}

	public function post_save($product_id,$variance_id)
	{
		$this->load->model('nitrocart/admin/products_variances_admin_m');
		$this->load->model('nitrocart/e_attributes_m');

		$input = $this->input->post();

		$len = strlen ('e_attribute_id_');

		$varaince_id = 0;

		$tmp_name = '';
		$first = true;
		
		foreach ($input as $key => $value) 
		{
			$v = substr($key, 0, $len );
			$_pre_id = substr($key, $len );

			if($v == 'e_attribute_id_')
			{
				$id = (int) $_pre_id;
				if($id > 0)
				{
					$attr = $this->e_attributes_m->get($id);
					$tmp_name .= (($first)?'':'&') . $attr->e_label . '=' . $value ;
					$this->e_attributes_m->set_value($id, $value);
					$first = false;
				}
			}
		}

		//now rename the variance
		if(isset($input['rename']))
		{
			$update = ['name'=>$tmp_name,'id'=>$input['variance_id'] ];
			$this->products_variances_admin_m->edit($product_id , $input['variance_id'], $update);	
		}

		redirect(NC_ADMIN_ROUTE.'/product/edit/'.$product_id.'#price-tab');
	}

	public function ajax_set($product_id, $variance_id)
	{
		//update
		$this->load->model('nitrocart/admin/products_variances_admin_m');
		$returnArray = $this->products_variances_admin_m->edit_shipping( $variance_id , $this->input->post() );
		echo json_encode($returnArray);die;	
	}

}