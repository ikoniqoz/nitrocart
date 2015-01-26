<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Productsadmin extends Public_Controller
{
	public $data;

	public function __construct()
	{
		parent::__construct();
		Events::trigger('SHOPEVT_ShopPublicController');

		$pass = false;
		if($this->current_user)
		{
			if($this->current_user->group == 'admin')
			{
				$pass= true;
			}
		}

		//only admin can view this page
		$pass OR redirect(NC_ROUTE);

		// Retrieve some core settings
		$this->template->title(Settings::get('shop_name'));

		$this->data = new ViewObject();

		$this->template
				->set_breadcrumb('Home', '/')
				->set_breadcrumb(Settings::get('shop_name'),'/'.NC_ROUTE);

	}


	/**
	 * This displays the list of ALL products.
	 * @uri yourdomain.com/shop/products
	 */
	public function index( $offset = 0 )
	{
	}

	/**
	 * Admin view only
	 */
	public function product($id = '')
	{
		$this->load->model('nitrocart/products_front_m');
		$this->data->product = $this->products_front_m->get_product($id, false , false );
		if(! $this->data->product)
		{
			$this->session->set_flashdata(JSONStatus::Notice,'Could not find product.');
			redirect(NC_ROUTE);
		}
		$this->template
			->enable_parser(true)
			->set('product',$this->data->product)
			->set_breadcrumb('Products' , NC_ROUTE.'/products')
			->set_breadcrumb('Product ( '. $this->data->product->name . ' )')
			->build('common/product_detail');
	}

}