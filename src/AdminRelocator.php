<?php namespace Nitrocart;

use Nitro\Relocator;

class AdminRelocator extends \Nitro\Relocator 
{
	/**
	 * Redirect to a product
	 */
	static public function ToProductIf( $product_id = false, $message='', $status='success' )
	{
		self::RelocateIf( $product_id, NC_ADMIN_ROUTE.'/product/edit/'.$product_id, $status, $message );
	}

	/**
	 * Redirects to products list
	 */
	static public function ToProductsIf( $condition=true , $message='An error occured.', $status='error' )
	{
		self::RelocateIf( $condition, NC_ADMIN_ROUTE . '/products', $status, $message );
	}	
}