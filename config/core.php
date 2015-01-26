<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * !! NO NOT ALTER !!
 * 
 *
 * Please take care with this setting.
 * Please do not add/prepend the 'admin'
 * This MUST be the same name as the folder
 * that contains the src.
 *
 * Future enhancements will allow for custom
 * admin routes so the config setting should NOT
 * be changed
 */

$config['core/config']  							= 'common'; 


/**
 * Which config file should we use for installing
 * This can be handy if you install multiple sites and you may have
 * a pre-determed config for variou client types.
 *
 * All config items sould be set and for a template look at
 * _default_install_config.php.bak
 * 
 */
$config['core/install/settings']  					= 'common';





/*
 * This MUST match the name of the folder
 * Note: that you cant simply just change
 * the name of the folder and this variable.
 * Please NEVER change this value.
 * 
 */
$config['core/path']  								= 'nitrocart';
$config['core/admin_route']  						= 'nitrocart';




/*
 * The core route to use
 */
$config['core/route']  								= 'store';













/*
 * Allow base amount pricing for products
 * 
 */
$config['core/base_amount_pricing']  				= false;  





/**
 * This will require a shipping option
 * to be setup and enabled or the customer can not checkout
 * until a shipment method is selected.
 */
$config['core/require_shpping_options']  			= true;  




/**
 * @deprecated  
 * 
 * Role base override
 *
 * Allows a user assigned to a role to 
 * view any feature that is hidden. However this is not fully implemented
 */
$config['core/allow_rbo']  							= false;  