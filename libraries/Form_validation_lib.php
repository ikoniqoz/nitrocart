<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Form_validation_lib extends CI_Form_validation
{
    public function error_as_array()
    {
        return $this->_error_array;
    }
}