<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

/**
 *
 * @return The Country object[data] from the db by passing the 2 letter code or country id
 * @param by = code2 | id
 * @param id = the 2 letter country code or Country ID
 *
 *
 */
if (!function_exists('validate_product_against_active_list'))
{
    function validate_product_against_active_list($selected_county_id = -2740, $zone_id=0 )
    {
		$ci =& get_instance();    	
        $allowed_countries = $ci->db->where('zone_id', $zone_id)->get('nct_zones_countries')->result();

        foreach ($allowed_countries as $c) 
        {
            if($selected_county_id == $c->country_id)
            {
                return true;
            }
        }
        return false;
	}
}

if (!function_exists('validate_product_against_master_list'))
{
    function validate_product_against_master_list($selected_county_id = -2740,$master_country_list = array() )
    {
		$ci =& get_instance();    	
        foreach($master_country_list as $country)
        {
            if($country->id == $selected_county_id)
                return true;
        }
        return false;
	}
}


if (!function_exists('nc_fetch_country_labels'))
{
    function nc_fetch_country_labels($address_list=array())
    {
		$ci =& get_instance();        	
        foreach($address_list as $key => $an_address)
        {
            if($row = $ci->db->where('code2',$an_address->country)->get('nct_countries')->row())
            {
                $address_list[$key]->country_label = $row->name;
            }
            else
            {
                $address_list[$key]->country_label =$an_address->country;
            }
        }

        return $address_list;
	}
}