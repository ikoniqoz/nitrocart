<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class NitroCart extends Admin_Controller
{
	// Set the section in the UI - Selected Menu
	protected $section = 'dashboard';

	public function __construct()
	{
		parent::__construct();
        redirect(NC_ADMIN_ROUTE.'/dashboard');
	}

	/**
	 */
	public function index()
	{
	}

}