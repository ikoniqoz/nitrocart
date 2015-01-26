<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
/*
 * Now we can define all our classes
 */
class Store_module extends ViewObject
{
    
    public function __construct()
    {
    	parent::__construct();    	
        $this->load->driver('Streams');
    }

	public function install($installer=NULL)
	{
		return false;
	}
	public function uninstall($installer=NULL)
	{
		return false;
	}
	public function upgrade($installer,$old_version)
	{
		return false;
	}
	public function health_check( $installer ) /*$as_bool = true */
	{
		return false;
	}

    protected function _createStreamTable($item=[])
    {
        if ( $stream_id  = $this->streams->streams->add_stream( $item['title'] , $item['assign_to'], $item['namespace'] , $item['prefix'] ,  $item['desc']  , array() )) 
    	{
    		return $stream_id;
    	}
    	return false;  
    }	

    protected function _createStreamFields($fields_array=[])
    {
    	return $this->streams->fields->add_fields($fields_array);
    }

    protected function _remove_stream($namespace)
    {
		$this->streams->utilities->remove_namespace($namespace);
    }

}