<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

use Nitrocart\Ui\Presenters\PublicProductPresenter as PublicProductPresenter;

class Products extends Public_Controller
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

		$this->limit = Settings::get('shop_qty_perpage_limit_front');
		$this->shop_name = Settings::get('shop_name');
		$this->data = new ViewObject();


		//default to list
		$this->limit = Settings::get('shop_qty_perpage_limit_front');
		$this->view_mode = ($this->session->userdata('products_view_mode')) ? $this->session->userdata('products_view_mode') : 'list';
		$this->order_by =  ($this->session->userdata('products_ordering_by')) ? $this->session->userdata('products_ordering_by') : 'id';
		$this->order_by_dir =  ($this->session->userdata('products_ordering_by_order')) ? $this->session->userdata('products_ordering_by_order') : 'asc';
		$this->per_page = $this->limit = ($this->session->userdata('products_per_page')) ? $this->session->userdata('products_per_page') : 10;
	

		$this->presenter = new PublicProductPresenter($this);

		/*
		$this->template
				->set_breadcrumb('Home', '/')
				->set_breadcrumb(Settings::get('shop_name'),'/'.NC_ROUTE);	
		*/
	}

	/**
	 * This displays the list of ALL products.
	 * @uri yourdomain.com/shop/products
	 */
	public function index( $offset = 0 )
	{
		
		$this->load->model('nitrocart/products_front_m');


		$total_items = $this->products_front_m->count_all();


		//  Build pagination for these items
		$pagination = create_pagination( NC_ROUTE . '/products/' , $total_items, $this->limit, 3);


		$products =  $this->products_front_m->order_by($this->order_by,$this->order_by_dir)->limit( $pagination['limit'] )->offset( $pagination['offset'] )->get_all();

		//get the view file that exist for products
		$view_file = $this->getProductListViewFile();


		$sort_by = $this->order_by . '/' . $this->order_by_dir;

		// finally
		$this->template
			->title(Settings::get('shop_name'), 'All Products')		
			->set_breadcrumb('Products')
			->set('product_count',$total_items)
			->set('pagination', $pagination )
			->set('offset',$offset)
			->set('limit',$this->limit)
			->set('per_page',$this->limit) //for compatibility we duplicate the value
			->set('sort_by',$sort_by)
			->set('products',$products)
			->set('viewmode',$this->view_mode)		
			->set('view_title','Products')
			->build('common/'.$view_file);		
	}



	public function type( $type_id, $offset = 0 )
	{

		$this->load->model('nitrocart/products_front_m');

		$f_field = is_numeric($type_id)? 'type_id' : 'type_slug' ;


		$total_items = $this->products_front_m->count_by( array( $f_field => $type_id,'deleted' => NULL ) );

		//  Build pagination for these items
		$pagination = create_pagination( NC_ROUTE . '/products/' , $total_items, $this->limit, 3);


		$products =  $this->products_front_m->where('type_id', $type_id )->order_by($this->order_by,$this->order_by_dir)->limit( $pagination['limit'] )->offset( $pagination['offset'] )->get_all();

		// finally
		$this->template
			->title(Settings::get('shop_name'), 'Products by Type')				
			->set_breadcrumb('Products')
			->set('product_count',$total_items)
			->set('pagination', $pagination )
			->set('offset',$offset)
			->set('limit',$this->limit)
			->set('products',$products)
			->set('viewmode',$this->view_mode)		
			->set('view_title','Products')
			->build('common/products_list');
	}

	/**
	 * User post by filter function
	 */
	public function orderby($order_by=NULL,$order='asc')
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
			if($limit = $this->input->post('display_per_page'))
			{
				$this->per_page = is_numeric($limit)?(int)$limit:$this->per_page;
				$this->session->set_userdata('products_per_page', $this->per_page);
			}			
		}
		$this->_setOrderBy($order_by,$order);	
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/products';			
		redirect($this->refer);	
		//if ajax, we should return a status ?
	}

	public function _setOrderBy($order_by=NULL,$order_dir='asc')
	{
		$order_by =  strtolower($order_by);
		$approved_values = array( 'id','name','slug','ordering_count','created','updated','created_by','views','featured');
		//if the order by is not in the approved order by we set to ID
		if (!(in_array($order_by, $approved_values)) )
		{
			$order_by = 'id';
		}
		$order_dir = (strtolower($order_dir) == 'asc')?'asc':'desc';
		$this->session->set_userdata('products_ordering_by', $order_by );
		$this->session->set_userdata('products_ordering_by_order', $order_dir);		
		return true;
	}


	public function sku($product_id, $variant_id)
	{

		$this->load->model('nitrocart/products_front_m');
		$this->load->model('nitrocart/e_attributes_m');

		// Get the product and all its goodness
		$product = $this->products_front_m->get_product( $product_id );


		if($product)
		{

			if($product->variant = $this->db->where('id',$variant_id)->get('nct_products_variances')->row())
			{
				$product->attributes = $this->e_attributes_m->get_by_variance_id($variant_id);
				var_dump($product);die;
			}
			else
			{
				echo "Y";die;
			}

		}
		else
		{
			echo "T";die;
		}

		//set message
		$this->session->set_flashdata(JSONStatus::Error,'Unable to find product..');
		redirect( NC_ROUTE.'/products');	
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
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/products';		
		redirect($this->refer);			
	}



	/**
	 * @param   [admin_as_customer]  Bool set to 'customer' if you are an admin and want to view the product as a customer
	 * @description If the system doesnt find the product it will redirect away
	 * EX : domain.com/products/product/7/customer
	 *
	 * OPTION 1 : domain.com/products/product/slug
	 * OPTION 3 : domain.com/products/product/7
	 */
	public function product($id = '')
	{
		$this->load->model('nitrocart/products_front_m');

		if($product = $this->products_front_m->get_product($id,true,true ))
		{

			$this->setLayoutForShopProduct( $product );

			$this->template
				->enable_parser(true)
				->title(Settings::get('shop_name'), 'Product ( '. $product->name . ' )')				
				->set('product',$product) //deprecated, remove this line
				->set_breadcrumb('Products', NC_ROUTE.'/products')
				->set_breadcrumb('Product ( '.$product->name.' )')
				->build('common/product_detail');	
		}
		else
		{
			$this->session->set_flashdata(JSONStatus::Error, lang('nitrocart:common:product_not_found') );
			redirect( NC_ROUTE );
		}

	}


	private function setLayoutForShopProduct($product)
	{

		$first 		= "nitrocart_product_{$product->slug}.html"; 	
		$second 	= "nitrocart_product_type_{$product->type_slug}.html";
		$third 		= 'nitrocart.html';
		$fourth 	= 'default.html';

		if($this->template->layout_exists($first))
		{
			$this->template->set_layout($first);
		}
		else if($this->template->layout_exists($second))
		{
			$this->template->set_layout($second);
		}
		else if($this->template->layout_exists($third))
		{
			$this->template->set_layout($third);
		}
	}

	private function getProductListViewFile()
	{
		if($this->view_mode=='grid')
		{
			return 'products_grid';
		}

		return 'products_list';
	}	


}