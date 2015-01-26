<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 *
 * Preferred value is uncg
 * 
 *
 * What type of regions to display
 * Upon install we need to setup the type of global regions to use
 * 
 */
$config['global_regions/type']  					= 'uncg';  //simple or uncg       




/**
 * Personal choice however if set to true you must set up
 * tax feature
 */
$config['install/tax_field_is_required']  			= false;




/**
 * If set to true once the shop is installed
 * the store will be set to open and additional default data
 * will exist to h
 */
$config['install/default_store_open']  				= true;






$config['install/default_store_name']  				= 'Store';



/**
 * The default Paymant notification email
 * This can be changed at any time. Just the default for install
 */
$config['install/default_payment_notification_email'] = 'admin@localhost';


/**
 * The default Order notification email
 * This can be changed at any time. Just the default for install
 */
$config['install/default_order_notification_email']  = 'admin@localhost';



/**
 * If set to true the manual/Bank payment 
 * method will be installed and on by 
 * default This is intended to speed up  
 * development testing. Best to turn this to 
 * false for production installs.
 */
$config['install/default_gateway']  				= true;







/**
 * Setup a default shipping method.
 * false by default but useful for testing
 */
$config['install/default_shipping']  				= true;





/**
 * The default items to display per page on 
 * the public site Note if you have a nitrocart 
 * installation and just want to change this 
 * value do so in the manage stoer section. 
 * This portion only reflects the default settings 
 * during install
 */
$config['install/default_store_perpage_limit']  	= 10;




/**
 * Installs text and code fields for 
 * use for products
 */
$config['install/default_product_fields']  			= true;




/**
 * Installs a NO TAX record.
 * This is recomended if require tax_id 
 * is set to true
 */
$config['install/default_taxes']  					= true;





/*
 * If set to true during the installation process
 * all countries will be setup. 
 * 
 * If false admins will have to setup countries 
 * manually Also note that if set to true the only 
 * values setup are CODE2,Country Name and region 
 * using `simple` region format .
 */
$config['install/default_countries']  				= true;






/*
 * Install a default package and package group
 * for quick start. Set this to false for advanced 
 * stores/users who want full control
 * as the default packages can not be deleted.
 */
$config['install/default_packages']  				= true;





/*
 * Should we create a default product type
 * This is the standard product type
 */
$config['install/default_product_type']  			= true;






/**
 * These are the sample products that will be installed
 * if `install/sample_products` is set to true
 *
 * Sample array:
 * [
 *      'Floral drop-waist dress'   => ['price' =>   69.95, 'type'=>'clothing'],
 *      'Dell XPS 13'               => ['price' => 1329.49, 'type'=>'computer'],
 * ]
 *
 */
$config['install/sample_products_data']  			=   
[
    'Floral drop-waist dress'   => ['price' =>   69.95, 'type'=>'clothing'],
    'Flashlight'                => ['price' =>   79.80, 'type'=>'electronics'],
    'Dell XPS 13'               => ['price' => 1329.49, 'type'=>'computer'],
    'Possibly standard'         => ['price' => 5,       'type'=>'standard'],
];


/**
 * The name of the default product type
 * Once installed the user can not delete this type but can 
 * remove/delete any new types they define.
 */
$config['install/default_product_types']  		= 
[
    'Standard'      => 
    [
        'default'=> 0,
        'core' => 1,
        'attributes'=> []
    ],
    'Clothing'      => 
    [
        'default'=> 1, 
        'attributes'=>
        [
            'Size',
            'Color',
            'Pattern',
        ]
    ],
    'Electronics'   => 
    [
        'default'=> 0,
        'attributes'=>
        [
            'MegaPixels',
            'MB',
        ]
    ],   
    'Computer'      => 
    [
        'default'=>0,
        'attributes'=>
        [
            'Operating System',
            'Screen',
            'Frame Rate',
        ]
    ],    
];




/**
 * Warning:: Can not contain spaces
 *
 * Use false if no attributes are to be instaled
 * 
 * Install default attributes.
 * This will not assign the attributes but will install
 * them to save time, allocating the attributes will still need
 * to be done at a later stage after installation
 */
$config['install/default_product_attributes']  		= 'UK Size|US Size|Size|Color|Colour|Width|Length|Height|MegaPixels|MB|Weight|Occasion|Style|Operating System|Screen|Frame Rate|DPI|';







/*
 * NOTE!! 
 * 
 * Please leave this set to false
 *
 */

$config['uninstall/extensions_fast_uninstall']  	= false;





/**
 * Same as `extensions_fast_uninstall` but 
 * this is specifically for the core features.
 * This can be set to true, if true the core 
 * will uninstall eachfeature If false, the 
 * admin will have to uninstall each feature first
 */
$config['uninstall/features_fast_uninstall']  		= true;