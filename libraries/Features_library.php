<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Features_library extends ViewObject
{



	protected $class_prefix = '';
	protected $class_suffix = ''; 


	public $dir_path = '';
			



	public function __construct()
	{
		$this->load->helper('directory');
		$this->dir_path =  dirname(__FILE__) . '/features';
	}




	/**
	 *
	 * @see http://ellislab.com/codeigniter/user-guide/helpers/directory_helper.html  - Directory_Helper
	 *
	 * @return Array: returns the array of Object Instance that are not installed
	 */
	public function get_drivers()
	{

		//only map 1 depth - level of directories
		$map = directory_map( $this->dir_path , 1);

		//var_dump($map);die;
		$uninstalled_objects = [];


		foreach ($map as $key => $folder)
		{


			if (is_dir($this->dir_path .'/' . $folder))
			{

				//remove ALL directory listings
				unset($map[$key]);
			}
			else 
			{

				$lib_object = $this->get_object( $folder );

				if ( $lib_object )
				{
					$lib_object->installed = 0;
					$obj = $lib_object;
					$uninstalled_objects[$obj->driver] = $obj;
				}

			}
		}

		return $uninstalled_objects;
	}


	public function exists($driver)
	{
		$filename = $this->dir_path .'/' . $driver;
		$lib_object = $this->get_object( $filename );
		return ($lib_object)?true:false;
	}
	public function get($driver)
	{
		$filename = $driver . '.php';
		$lib_object = $this->get_object( $filename );
		return $lib_object;
	}

	

	

	/**
	 * Basically validates the folder and makes sure that
	 * inside the folder is a valid gateway
	 *
	 * @param String $slug
	 * @return < Object Gateway | NULL>
	 */
	protected function get_object($filename)
	{



		$filename = trim($filename);

		# Get gateway path location
		$path_to_feature =  $this->dir_path . '/' . $filename;

	

		# check
		if (is_file($path_to_feature))
		{

			#include the full path to file
			include_once $path_to_feature;


			$class_Name =  ucfirst(str_replace('.php', '', $filename));


			//echo $LibraryObjectClass;die;
			if (class_exists($class_Name))
			{
				$lib_object = new $class_Name;

				if($lib_object->system_type == 'feature')
					return $lib_object;
			}

		}

		#oops
		return NULL;
	}


}