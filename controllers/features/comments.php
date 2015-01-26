<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Comments extends Public_Controller
{
	public $data;

	public function __construct()
	{
		// Initialize
		parent::__construct();
		Events::trigger('SHOPEVT_ShopPublicController');

		//Check if module is installed
		system_installed_or_die('feature_product_comments','/');


		// Is the Store Open ?
		Settings::get('shop_open_status') OR redirect( NC_ROUTE . '/closed');

		// Where did we come from
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE.'/products';	

		$this->data = new ViewObject();

	}

	/**
	 * This displays the list of ALL products.
	 * @uri yourdomain.com/shop/products
	 */
	public function index()	{	}



	/**
	 * set view mode = lit|grid
	 */
	public function add($product_id)
	{
	
		$this->load->model('nitrocart/features/comments_m');
		$comment = $this->input->post('comment');

		if($comment)
		{
			$data = [
					'product_id' 	=> $product_id,
					'comment' 		=> $comment,
					'reffered'		=> $this->refer,
					];

			$this->comments_m->create($data);
		}

		redirect($this->refer);				
	}


	public function flag($comment_id)
	{
		$this->load->model('nitrocart/features/comments_m');

		$this->comments_m->flag($comment_id);

		redirect($this->refer);				
	}


}