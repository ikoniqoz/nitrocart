<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Systems_m extends MY_Model
{

    public $_table = 'nct_systems';


    public function __construct()
    {
        parent::__construct();
    }

    public function get($driver)
    {   
        return $this->db
            ->select('*', false)
            ->where('driver',$driver)
            ->get($this->_table)
            ->row();
    }

    public function create($input)
    {
    }

    public function set_value($driver = '', $installed = 0)
    {
        return $this->db->update($this->_table, ['installed'=>$installed], ['driver' => $driver]);
    }
    public function set_driver_value($driver = '', $installed = 0)
    {
        return $this->db->where('driver',$driver)->update($this->_table, ['installed'=>$installed]);
    }

    public function get_installed_features()
    {
        return $this->where('installed',1)->where('system_type','feature')->get_all();
    }


    public function delete($id)
    {
        return false;
    }

}