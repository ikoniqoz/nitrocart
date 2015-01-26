<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Comments_m extends MY_Model
{

    public $_table = 'nct_products_comments';
    
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'nct_products_comments';
	}

}