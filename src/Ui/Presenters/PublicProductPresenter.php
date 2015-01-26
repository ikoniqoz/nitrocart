<?php namespace Nitrocart\Ui\Presenters;

use \Nitro\Ui\Presenters\CorePresenter;

/**
 * @author Sal Bordonaro
 */
class PublicProductPresenter extends \Nitro\Ui\Presenters\CorePresenter
{

	public function __construct($config)
	{
		//set the parent defaulta
		parent::__construct();

		$this->shop_name = $config->shop_name;

		//default to list
		//$this->limit = $config->limit;
		$this->view_mode = ($config->session->userdata('products_view_mode')) ? $config->session->userdata('products_view_mode') : 'list';
		$this->order_by =  ($config->session->userdata('products_ordering_by')) ? $config->session->userdata('products_ordering_by') : 'id';
		$this->order_by_dir =  ($config->session->userdata('products_ordering_by_order')) ? $config->session->userdata('products_ordering_by_order') : 'asc';
		$this->per_page = $this->limit = ($config->session->userdata('products_per_page')) ? $config->session->userdata('products_per_page') : 10;


		$this->template
				->enable_parser(true)
				->set_breadcrumb('Home', '/')
				->set_breadcrumb($this->shop_name,'/'.NC_ROUTE);

	}

	public function display_list($products)
	{
		$a = (object)[];
		$this->template->title($this->shop_name, 'All Products');



		$a->offset = $offset;
		$a->limit = $this->limit;
		$a->per_page = $this->limit;
		$a->sort_by = $this->order_by . '/' . $this->order_by_dir;
		$a->products = $products;
		$a->viewmode = $this->view_mode;
		$this->default_view = 'common/'.$this->getProductListViewFile();
		parent::present($a);	
	}
}