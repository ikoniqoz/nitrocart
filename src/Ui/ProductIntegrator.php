<?php namespace Nitrocart\Ui;

use Nitro\Ui\Integrator;

/**
 * @author Sal Bordonaro
 */
class ProductIntegrator extends \Nitro\Ui\Integrator {

	/**
	 * @constructor
	 */
	public function __construct() {
		// Get the ci instance
		parent::__construct();
	}

	
	public static function SetupUI(&$product)
	{
		$product->module_tabs = [];
		$product->modules = [];
	}

	public static function RequestProductAdminTabs(&$product)
	{
		//prod_tab_order
		usort($product->module_tabs, "sort_module_tabs");		
	}
}