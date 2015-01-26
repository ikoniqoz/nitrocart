<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
final class Nc_cp extends ViewObject
{

    public function __construct()
    {
		parent::__construct();
	}

	public function get_common_sections_menu()
	{
		$info = [];
		$info['dashboard'] = array(
			'name' => 'nitrocart:admin:dashboard',
			'uri' => NC_ADMIN_ROUTE.'/dashboard',
			'shortcuts' => []
		);

        if(Settings::get('shop_store_type')=='standard')
        {		
	        $info['orders'] = array(
				'name' => 'nitrocart:admin:orders',
				'uri' => NC_ADMIN_ROUTE.'/orders',
				'shortcuts' => []
			);
    	}
		$info['products'] = array(
			'name' => 'nitrocart:admin:products',
			'uri' => NC_ADMIN_ROUTE.'/products',
			'shortcuts' => []
		);
		return $info;
	}
	
}