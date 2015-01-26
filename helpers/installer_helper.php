<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

/**
 * Gets the title of the settings by slug
 */
if (!function_exists('sih_slug2title'))
{
	function sih_slug2title( $driver , $by = 'driver' )
	{
		$ci =& get_instance();
		if($ci->db->table_exists('nct_systems'))
		{
			$settings = $ci->db->where('driver',$driver)->get('nct_systems')->row();
			return ($settings) ? $settings->title : '' ;
		}
		return '';
	}
}