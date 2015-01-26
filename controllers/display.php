<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Display extends Public_Controller
{
	public $data;
	protected $view_mode;//grid list
	protected $order_by; //field,count,created_by
	protected $order_by_dir; //asc,desc
	protected $per_page; //asc,desc

	public function __construct()
	{
		parent::__construct();
		Events::trigger('SHOPEVT_ShopPublicController');

		Settings::get('shop_open_status') OR redirect( NC_ROUTE . '/closed');

		//default to list
		$this->limit 		= Settings::get('shop_qty_perpage_limit_front');
		$this->view_mode 	= ($this->session->userdata('products_view_mode')) ? $this->session->userdata('products_view_mode') : 'list';
		$this->order_by 	= ($this->session->userdata('products_ordering_by')) ? $this->session->userdata('products_ordering_by') : 'id';
		$this->order_by_dir = ($this->session->userdata('products_ordering_by_order')) ? $this->session->userdata('products_ordering_by_order') : 'asc';
		$this->per_page 	= ($this->session->userdata('products_per_page')) ? $this->session->userdata('products_per_page') : $this->limit ;
		$this->refer 		= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/products';

	}

	/**
	 * This displays the list of ALL products.
	 * @uri yourdomain.com/shop/products
	 */
	public function index()
	{
		show_404();
	}



	public function filter()
	{
		if($this->input->post())
		{
			if($filter = $this->input->post('display_list_filter'))
			{
				$split = explode('/',$filter);

				if(count($split)>1)
				{
					// check we have 2 in the array
					$order_by = $split[0];
					$order = $split[1];
				}
			}
		}

		$this->_setOrderBy($order_by,$order);			
		redirect($this->refer);	
		//if ajax, we should return a status ?
	}

	public function _setOrderBy($order_by=NULL,$order='asc')
	{

		$order_by =  strtolower($order_by);
		$order_dir = strtolower($order);

		$approved_values = array( 'id','name','slug','ordering_count','created','updated','created_by','views','featured');

		if($order_by=='price')
		{
			//lets do some magic here to make this happen
		}

		//if the order by is not in the approved order by we set to ID
		if (!(in_array($order_by, $approved_values)) )
		{
			$order_by = 'id';
		}

		$order_dir = ($order_dir == 'asc')?'asc':'desc';

		$this->session->set_userdata('products_ordering_by', $order_by );
		$this->session->set_userdata('products_ordering_by_order', $order_dir);		
		return true;
	}


	/**
	 * set view mode = list|grid
	 */
	public function setviewmode($value='list')
	{
		if($value == 'grid' OR $value =='list')
		{	
			$this->session->set_userdata('products_view_mode',$value);		
		}
		redirect($this->refer);			
	}

}