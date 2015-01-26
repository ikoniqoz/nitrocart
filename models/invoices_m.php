<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Invoices_m extends MY_Model
{
    public $_table = 'nct_invoices';

	public function __construct()
	{
		parent::__construct();
	}
}