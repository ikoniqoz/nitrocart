<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
/*
 * thanks to Christian gup.
 */
class Routes_library extends ViewObject
{

	private static $__GLOBAL_ROUTE_PATH;
	private static $__BAK_ROUTE_PATH;

	public function __construct()
	{
		$this->load->helper('file');
		$this->load->model('nitrocart/admin/routes_admin_m');

		self::$__GLOBAL_ROUTE_PATH = $this->get_global_route_path();
		self::$__BAK_ROUTE_PATH = $this->get_bak_route_path();		
	}

	/**
	 * Rebuilds the database file with the new routes
	 */
	public function rebuild_db( $new_route = NC_ROUTE )
	{
		$all = $this->db->get('nct_routes')->result();

		foreach($all as $r)
		{
			$this->db->where('id',$r->id)->update('nct_routes', ['uri' => $new_route . $r->default_uri] );
		}

		$this->rebuild();

		return true;
	}


	/*
	 * This gets called to re-write the global system/config/routes.php file
	 *
	 *
	 * BEWARE: this is experiemental and NOT stable. Some times this wipes out the
	 * file and you can lose the file.
	 * Make a backup of that file.
	 */
	public function nitrocart_install()
	{

		$this->create_route_bakup(true);

		return $this->rebuild();
	}

	/**
	 * Create the default_route.php backup file
	 */
	private function create_route_bakup($overwrite=false)
	{

		//Check if we have a default route file
		if( ( ! file_exists( self::$__BAK_ROUTE_PATH ) ) OR ($overwrite) )
		{
			copy( self::$__GLOBAL_ROUTE_PATH , self::$__BAK_ROUTE_PATH );
		}

		if( file_exists( self::$__BAK_ROUTE_PATH ) ) 
		{
			return true;
		}

		return false;
	}

	/**
	 * If a default_route.php file exist in /bak/system/default_route.php
	 * Then we need to restore the original file back to cms/config/routes.php
	 */
	public function nitrocart_uninstall()
	{

		if( file_exists(self::$__BAK_ROUTE_PATH) )
		{
			//Copy the default one
			copy( self::$__BAK_ROUTE_PATH , self::$__GLOBAL_ROUTE_PATH );

			//Delete old backup copy
			unlink( self::$__BAK_ROUTE_PATH );
		}

		return true;
	}	

	/**
	 * Take a snapshot of the global route file
	 */
	public function route_snapshot()
	{
		$path = $this->get_snapshot_path();

		//Check if we have a default route file
		if( ! file_exists( $path ) )
		{
			copy( self::$__GLOBAL_ROUTE_PATH , $path );
		}

		return true;
	}	


	/**
	 * take the backup and merge with the db
	 * write the result
	 * if the backup doesnt exist dont do anything
	 * @param $ignore_routes  = array('custom')
	 */
	public function rebuild($ignore_routes=array())
	{

	    $routes = $this->get_db_routes( $ignore_routes );
	    if ( empty( $routes ) )
	    {
	    	return false;
	    }

		// Check to make sure that we can read/write the
		$route_file = self::$__GLOBAL_ROUTE_PATH;

		$info = get_file_info(self::$__GLOBAL_ROUTE_PATH, 'writable');

		// Does it even exist? Is it writable?
		if ( ! $info or ! $info['writable'])
		{
			return false;
		}


		// Check if we have a default route file - we need this to read from
		if( ! file_exists(self::$__BAK_ROUTE_PATH) )
		{
			//lets make a default route file and add to it.
			if($this->create_route_bakup(true))
			{
				if( ! file_exists(self::$__BAK_ROUTE_PATH) )
				{
					return false;
				}
				//else continue
			}
			else
			{
				return false;
			}

		}

		//That's the backup file
		$default_route_file = self::$__BAK_ROUTE_PATH;

		// Let's start our routes file!
		$file_data = read_file($default_route_file)."\n";
		$file_data = $this->get_content($routes, $file_data);


		// Clear the file first
		file_put_contents( self::$__GLOBAL_ROUTE_PATH , '');

		// Write the file
		return write_file( self::$__GLOBAL_ROUTE_PATH , $file_data, 'r+');
	}



	/**
	 * Get the routes from the Database
	 * @param $ignore: Array - array of modules to ignore when getting the outes
	 */
	private function get_db_routes($ignore=array('custom'))
	{

		if(count($ignore) > 0)
		{
			foreach($ignore as $key=>$value)
			{
				$this->routes_admin_m->where('module !=',$value);
			}
		}

		$routes = $this->routes_admin_m->get_all();

	    if ( empty( $routes ) )
	    {
	    	return false;
	    }

	    return $routes;	
	}


	/**
	 * Build the string/content for the new routes.php file
	 * 
	 * @param $routes : Array of routes
	 * @param $file_data : String - Existing route file content
	 * @return : String - The full content of the file
	 */
	private function get_content($routes=array(), $file_data='')
	{
		if ($routes)
		{
			$file_data .= "\n/* SHOP custom routes (!!WARNING!! - Do not change or alter these routes or Comments manually) */\n\n";

			foreach ($routes as $route)
			{
				$file_data .= "\$route['{$route->uri}'] = '{$route->dest}';\n";
			}
		}

		$file_data .= "\n".'/* End of file routes.php */';

		return $file_data; 
	}


	/**
	 * Gets the path of the global routes file
	 */
	private function get_global_route_path()
	{
		return APPPATH.'config/routes.php';		
	}


	/**
	 * Gets the path of the backed up copy of the routes file 
	 * .. bak/system/default_routes.php
	 */
	private function get_bak_route_path()
	{
		return NITROCART_INSTALL_PATH.'bak/system/default_routes.php';
	}


	private function get_snapshot_path( $file_prefix = 'route_' )
	{
		return NITROCART_INSTALL_PATH.'bak/system/snapshots/' . $file_prefix . time() . 'snapshot.php';
	}


}