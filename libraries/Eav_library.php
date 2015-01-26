<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Eav_library extends ViewObject
{

	public function __construct()
	{
		parent::__construct();

	}


	/**
	 * install various attributes for types of industries, if the attribute exist do not install
	 */
	public function install_attribute_set($type='clothing')
	{
		/*
		switch ($type) 
		{
			case 'clothing':
				array('Height','Width');
				break;
			
			default:
				# code...
				break;
		}
		*/
	}

}