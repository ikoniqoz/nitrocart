<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Search extends Public_Controller
{
	public $data;
	public $search_object;

	public function __construct()
	{
		parent::__construct();
		Events::trigger('SHOPEVT_ShopPublicController');

		Settings::get('shop_open_status') OR redirect( NC_ROUTE. '/closed');


		$this->data = new ViewObject();
		$this->search_object =  new ViewObject();

		$this->template
				->title( Settings::get('shop_name') )
				->set_breadcrumb('Home', '/')
				->set_breadcrumb( Settings::get('shop_name'),'/'.NC_ROUTE)
				->set_breadcrumb('Search');

	}


	/**
	 * This displays the list of ALL products.
	 * @uri yourdomain.com/shop/products
	 */
	public function index( $offset = 0 )
	{

		$this->limit = Settings::get('shop_qty_perpage_limit_front');

		$this->load->model('nitrocart/products_front_m');

		// Call Search for other modules
		Events::trigger('SHOPEVT_SearchProducts',$this->search_object);

		$total_items = $this->products_front_m->count_all();

		$products =  $this->products_front_m->limit( $this->limit , $offset )->get_all();

		//  Build pagination for these items
		$pagination = create_pagination( NC_ROUTE .'/products/' , $total_items, $this->limit);
		//$pagination = create_pagination( NC_ROUTE .'/products/' , $total_items, $this->limit, 3);		

		// finally
		$this->template
			->set_breadcrumb('Products')
			->set('product_count',$total_items)
			->set('pagination', $pagination )
			->set('offset',$offset)
			->set('limit',$this->limit)
			->set('products',$products)
			->build('common/products_list');

	}

	public function products_search()
	{
		$product_types = [];

		//leave price range empty array to include all or
		$product_price_ranges = [];


		$product_attributes = [];

		//prices
		//$product_price_range = ['start'=>0,'end'=>150]


		//if end is less than start, or not set!
		//$product_price_range = ['start'=>100]
		//$product_price_range = ['start'=>100,'end'=>-1]

		// for max only use the following/either combo
		//$product_price_range = ['end'=>700]
		//$product_price_range = ['start'=>0,'end'=>700]

		//for attributes, if some are sent we must filter them
		//attributes do have key=>value relations
		//$product_attributes = ['color' => 'Red','size'=>12];

	}

}