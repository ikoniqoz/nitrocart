<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Widget_Nitrocart_Cart extends Widgets
{
	public $title		= array(
		'en' => 'NitroCart Cart',
	);
	public $description	= array(
		'en' => 'Display a list of Cart Items',
	);
	public $author		= 'Salvatore Bordonaro';
	public $website		= 'http://inspiredgroup.com.au';
	public $version		= '2.1';

	public $fields = array();

	public function run($options)
	{
		$count = $this->mycart->total_items();

		if($count==NULL)
			$count = 0;

		return array(
				'total' => $this->mycart->total(),
				'items_count' => $count,
				'contents' => $this->mycart->contents(),

		);
	}
}
