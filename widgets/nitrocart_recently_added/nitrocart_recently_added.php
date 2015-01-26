<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Widget_Nitrocart_Recently_Added extends Widgets {

	public $title = array(
		'en' => 'Shop - Recenly Added',
	);
	public $description = array(
		'en' => 'Display recently added products to your site',
	);
	public $author		= 'Salvatore Bordonaro';
	public $website		= 'http://inspiredgroup.com.au';
	public $version		= '2.1';
	public $fields = array(
		array(
			'field' => 'max',
			'label' => 'Max',
			'rules' => 'required'
		)
	);

	public function run($options)
	{


		$this->load->model('nitrocart/products_front_m');

		//Get the last added record from the products table that is active
		$this->products_front_m->where('public', 1)->where('deleted', NULL)->order_by("id", "desc");

		$max = ($options['max'] > 0) ? $options['max']  : 1 ;

		$this->products_front_m->limit($max);

		$new = $this->products_front_m->order_by('created', 'desc')->get_all();

		return count($new) ? array('items' => $new) : false;
	}

}
