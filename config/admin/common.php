<?php defined('BASEPATH') OR exit('No direct script access allowed');


/*
 * If set to false, the user will not have access to the Feature
 * management section. However the menu Item will still display
 * 
 */
$config['admin/enable_features']  					= true;  






/*
 * Can admin delete their own orders?
 */
$config['admin/delete_orders']  					= true;  






/**
 * Display the Notes tab in admin/orders/order{n}
 */
$config['admin/orders/show_notes_tab']  			= true;  





/**
 * The items tab is not required since there is an incoice line item for each
 * product/variane sold.
 * However the items tab is handy as it has built in links
 * to prodcts and the variation part.
 */
$config['admin/orders/show_items_tab']  			= true;  



/**
 * Show the Transaction tab in admin/orders/order{n}
 */
$config['admin/orders/show_txn_tab']  				= true;  




/**
 * Show the points total accumulated in admin/orders/order{n}
 */
$config['admin/orders/show_points']  				= true;  




/**
 * Show the totals section in the details tab 
 * in admin/orders/order{n}
 */
$config['admin/orders/show_totals_on_details']  	= true;  




/**
 * Show which checkout options were set during checkout
 */
$config['admin/order/details/show_checkoutmethod']  = true;  




/**
 * Show the infobar in admin/orders/order{n}
 */
$config['admin/orders/show_info_status']  			= true;  




/*
 * The other tab in admin/products/edit 
 * shows info about the view files being used. 
 */
$config['admin/show_product_other_tab']  			= false;  



/**
 * Alows admin to view and change the 
 * featured option of a product.
 *
 * Some stores do not require the featured directive so 
 * it can simply be hidden from view
 */

/**
 * For edit veiw only
 */
$config['admin/show_product_views_field']  			= true;  



/**
 * For product edit view:
 * Show the points field - by default the value is 0
 */
$config['admin/show_product_points_field']  		= true;  




/**
 * Show the featured option, note this does not disable 
 * the field but simply hides it. Any of these field when hidden still
 * are active fields but are removed from admins view
 */
$config['admin/show_product_featured_field']  		= true;  




/**
 * Show the slug field
 */
$config['admin/show_product_slug_field']  			= true;



/**
 * Show the infobar
 */
$config['admin/product/show_infobar']  				= true;


/**
 * For list view
 */
$config['admin/show_products_views_field']  		= true;



/**
 * Show the featured column
 */
$config['admin/show_products_featured_field']  		= true;