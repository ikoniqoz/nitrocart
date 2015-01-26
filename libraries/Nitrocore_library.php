<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/

/*Nitrocore_library should be changed to store_bootrapper**/
final class Nitrocore_library extends ViewObject
{
    public function __construct()
    {
    	parent::__construct();

		$this->config->load('nitrocart/core');

        /**
         * Collect congig values
         * @var [type]
         */
        $route 			= $this->config->item('core/route');
        $namespace      = $this->config->item('core/admin_route');        
        $base_p         = $this->config->item('core/base_amount_pricing');
        $req_s          = $this->config->item('core/require_shpping_options'); 
        $rbo            = $this->config->item('core/allow_rbo');         
        $mod            = $this->get_path("modules/{$namespace}/");   

        $config_config = $this->config->item('core/config');       

        $this->do_def('NC_CONFIG', $config_config);

        /**
         * Define our NC constants
         */
        $this->do_def('NC_ROUTE', $route );
        $this->do_def('NC_ADMIN_ROUTE','admin/' . $namespace);
        $this->do_def('NC_BASEPRICING', $base_p);
        $this->do_def('NC_REQSHIPPING', $req_s);


        $this->do_def('NITROCART_INSTANCE', 'OK');
        $this->do_def('NITROCART_INSTALL_PATH', $mod."modules/{$namespace}/");
        $this->do_def('NITROCART_MOD_PATH', $mod);

		log_message('debug', "Nitrocore_library Library Class Initialized");
	}
    private function get_path($__path__)
    {
        if (is_dir(ADDONPATH.$__path__))
        {
            $mod = ADDONPATH;
        }
        elseif (is_dir(SHARED_ADDONPATH.$__path__))
        {
            $mod = SHARED_ADDONPATH;
        }
        elseif (is_dir(APPPATH.$__path__))
        {
            $mod = APPPATH;
        }
        return $mod;        
    }

    private function do_def($const_name,$value)
    {
        if (!(defined($const_name)) )
        {
            define($const_name, $value );
        }        
    }
}
