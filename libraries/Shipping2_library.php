<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Shipping2_library extends ViewObject
{

	public $fs_path = '';
	public $www_path = '';
	protected $class_suffix = '_ShippingMethod'; //used for geting the class object

	public function __construct()
	{
		parent::__construct();
		$this->load->model('nitrocart/shipping_options_m');
		$this->load->helper('directory');
		//Path to gateways for FileSystem
		$this->fs_path =  dirname(__FILE__) . '/shipping/';
		//Path to gateways for www access
		$this->www_path = base_url() . $this->module_details['path'].'/libraries/shipping/';
	}



	/**
	 * get all uninstalled + available methods
	 */
	public function get_all_available()
	{

		//only map 1 depth - level of directories
		$map = directory_map($this->fs_path, 1);

		$uninstalled_objects = [];

		foreach ($map as $key => $folder)
		{

			//echo $folder;
			if (!is_dir($this->fs_path . $folder))
			{
				//remove ALL directory listings
				unset($map[$key]);
			}
			else 
			{

				$lib_object = $this->load_shipping_class( $folder );

				# If Not false
				if (  $lib_object  )
				{
					# Add to list of acceptable gateways
					$uninstalled_objects[] = $lib_object;
				}

			}
		}

		return $uninstalled_objects;
	}

	/**
	 * get all installed 
	 *
	 * $this->shipping2_library->get_all_installed(); //all installed
	 * $this->shipping2_library->get_all_installed(true); //all enabled
	 * $this->shipping2_library->get_all_installed(false); //all disabled
	 *
	 */
	public function get_all_installed($enabled=false)
	{
		
		if($enabled===true)
		{
			$this->shipping_options_m->where('enabled',1);
		}
		else if($enabled===false)
		{
			$this->shipping_options_m->where('enabled',0);
		}
		else if($enabled==NULL)
		{

		}

		$a = $this->shipping_options_m->get_all();
		return $this->process_list($a);
	}

	/**
	 * Get a shippingmethod from DB by ID, and merge it with the class
	 */
	public function get_installed($id)
	{	
		$db = $this->shipping_options_m->get($id);
		return $this->merge($db,$db->slug);
	}

	/**
	 * Install a new option by slug
	 */
	public function install($slug)
	{
		$lib_object = $this->load_shipping_class($slug);

		if ( $lib_object )
		{	
			$name = $lib_object->name;
			return $this->shipping_options_m->install( $slug , $name );
		}

		return false;
	}

	/**
	 * Uninstall by id
	 */
	public function uninstall($id)
	{
		return $this->shipping_options_m->uninstall( $id );		
	}

	/**
	 * Enable a shipping option
	 */
	public function enable($id)
	{
		return $this->shipping_options_m->enable( $id );		
	}

	/**
	 * Disable a shipping option
	 */
	public function disable($id)
	{
		return $this->shipping_options_m->disable( $id );		
	}

	/**
	 * only allow a few fields to get through just in case
	 */
	public function save($input)
	{
		$edit = array();
		$edit['id'] 		= $input['id'];		
		$edit['title'] 		= $input['title'];
		$edit['description'] = $input['description'];
		//$edit['options'] 	= serialize("");

		//only if available
		if(isset($input['enabled']))
		{
			$edit['enabled'] = $input['enabled'];
		}
		
		//not all gateways have options
		if(isset($input['options']))
		{
			$edit['options'] = $input['options'];
		}

		return $this->shipping_options_m->edit( $edit );
	}

	/**
	 * Load the class from filesystem
	 */
	protected function load_shipping_class($slug)
	{

		$slug = trim($slug);

		# Get gateway path location
		$shipping_module_class_file =  $this->get_object_path($slug);


		# check
		if (is_file($shipping_module_class_file))
		{

			#include the full path to file
			include_once $shipping_module_class_file;


			$shippingObjectClass = $this->get_object_name($slug);


			if (class_exists($shippingObjectClass))
			{

				#create
				$lib_object = new $shippingObjectClass;


				# Slug
				$lib_object->slug = $slug;


				# Set Image
				$lib_object->image =  $this->get_object_image($slug);


				# Admin View
				$lib_object->form = $this->fs_path . $slug . '/views/form.php';

				# Client View
				$lib_object->display = $this->fs_path . $slug . '/views/display.php';

				#return
				return $lib_object;

			}
		}

		#oops
		return NULL;
	}


	/**
	 * merge the db record with the local file class
	 */
	private function merge($db_object)
	{
		$shipping_class 			= $this->load_shipping_class($db_object->slug);
		$shipping_class->image		= $this->get_object_image( $db_object->slug );
		$shipping_class->id			= $db_object->id;	
		$shipping_class->enabled	= $db_object->enabled;	
		$shipping_class->title		= $db_object->title;
		$shipping_class->form 		= $this->fs_path . $db_object->slug . '/views/form.php';
		$shipping_class->display 	= $this->fs_path . $db_object->slug . '/views/display.php';
		$shipping_class->options 	= $db_object->options;
		return $shipping_class;
	}

	/**
	 * Process list of db results
	 */
	private function process_list($list = array())
	{
		foreach($list as $key=>$l)
		{
			$list[$key]->image = $this->get_object_image($l->slug);
		}

		return $list;
	}

	/**
	 * get the name of teh class
	 */
	private function get_object_name($name)
	{
		return ucfirst(   strtolower(  trim($name)  )	) . $this->class_suffix  ;
	}

	/**
	 * get the path of the method
	 */
	private function get_object_path($slug)
	{
		return $this->fs_path . $slug . '/' . $slug . '.php';
	}

	/**
	 * get the default image of the method
	 */
	private function get_object_image($slug)
	{
		$fs_file = $this->fs_path.$slug.'/'.$slug.'.png'; //local
		$www_file = $this->www_path.$slug.'/'.$slug.'.png'; //public
		return (file_exists($fs_file)) ?  $www_file :  null ;
	}	


}