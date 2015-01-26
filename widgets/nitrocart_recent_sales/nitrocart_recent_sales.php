<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Widget_Nitrocart_Recent_sales extends Widgets
{
	public $title		= array(
		'en' => 'NitroCart - Recent Sales',
	);
	public $description	= array(
		'en' => 'List the top most recent orders with a link to open and view the order',
	);
	public $author		= 'Salvatore Bordonaro';
	public $website		= 'http://inspiredgroup.com.au';
	public $version		= '2.1';

	public function run($options)
	{

		$this->load->model('nitrocart/orders_m');

		$data['recent_shop_order'] = $this->orders_m->order_by('id','desc')->get_last(5);

		return $data;

	}

}
