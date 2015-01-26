<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Reports_library extends ViewObject
{

	public function __construct()
	{
		log_message('debug', "Class Initialized");
	}

	/**
	 * also remove hidden reports
	 */
	public function get_all_report_types()
	{
		$data = new ViewObject();

		$order_types = [
				'prodsold'		=>	['list' => 'Products', 'visible' => true, 'path'=>'nitrocart', 'name' => 'Products by Sale Value' 			, 'description' => 'View listing of products most sold by Value'],
				'mostsoldp'		=>	['list' => 'Products', 'visible' => true, 'path'=>'nitrocart', 'name' => 'Products Item QTY Sold' 			, 'description' => 'View listing of products most sold by QTY'],
				'mostviewed'	=>	['list' => 'Products', 'visible' => true, 'path'=>'nitrocart', 'name' => 'Products Most viewed' 			, 'description' => 'List the most viewed products of ALL time.'],														
				'highorders'	=>	['list' => 'Orders'  , 'visible' => true, 'path'=>'nitrocart', 'name' => 'Highest valued Orders ' 			, 'description' => 'List the highest revenue orders of ALL time.'],	
				'bestcustomers'	=>	['list' => 'Customer', 'visible' => true, 'path'=>'nitrocart', 'name' => 'Best Customers ' 					, 'description' => 'List the highest spending customers.'],					
			];

		foreach ($order_types as $key => $value) 
		{
			if($value['visible']==false) unset($order_types[$key]);
		}

		$data->report_list = $order_types;

		//See if anyone has some reports for us
		Events::trigger('SHOPEVT_AdminReportListGet', $data);

		return $data->report_list ;
	}

}