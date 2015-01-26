<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Core_admin_filter_m extends MY_Model
{

    /**
     * The default table for this model
     * @var string
     */
    public $_table = 'nct_products';
    protected $_filter;

    const FILTER_TYPE = 0;
    const FILTER_VALUE = 1;
    const REQ_ARG_COUNT = 2;
    const ARG_DELIMETER = '|';

    public function __construct()
    {
        parent::__construct();
    }


    protected function getArgs($f_filter)
    {
        //check if there is a PIPE |
        $arg = explode( self::ARG_DELIMETER , $f_filter ); 

        if( count($arg) == self::REQ_ARG_COUNT)
        {
            return $arg;
        }

        return false;
    }

    protected function setOrderBy($filter)
    {
        $dir ='asc';
        $field = trim($filter['order_by']);

        if (array_key_exists('order_by_order', $filter))
        {
            $dir = $filter['order_by_order'];
        }

        $this->setOrderByFilter( $field , $dir );
    }

    private function setOrderByFilter($field='id',$dir='asc')
    {
        $this->addFilter('order','order_by',$field,$dir);
    }


    protected function addFilter($filter,$action,$key,$value)
    {
        $this->_filter[$filter]['action'] = $action;
        $this->_filter[$filter]['key'] = $key;            
        $this->_filter[$filter]['value'] = $value;   
    }

    /**
     * Only ads the search string is a string exist
     */
    protected function addStringExistFilter($filters=[], $filter_name , $action , $key , $must_exist=true)
    {
        if (array_key_exists($filter_name, $filters))
        {
            $value = trim($filters[$filter_name]);
            
            if($must_exist)
            {
                if( $value == "" )
                {
                    return true;
                }
            }
         
            $this->addFilter( $filter_name , $action, $key , $value );         
        }

        return true;
    }

    protected function addYesNoAllFilter($filters=[], $filter_name ,$key)
    {
        return $this->addOnOffAllFilter($filters, $filter_name ,$key, 'yes', 'no', 'all');
    }

    protected function addOnOffAllFilter($filters=[], $filter_name ,$key, $on='on',$off='off',$all='all')
    {

        if (array_key_exists( $filter_name, $filters ))
        {
            switch($filters[$filter_name])
            {
                case $on:
                    $this->addFilter($filter_name,'where',$key, 1 );
                    break;
                case $off:
                    $this->addFilter($filter_name,'where',$key, 0 );
                    break;   
                case $all:                    
                default:
                    //do nothing
                    break;                                                             
            }
        }

        return true;

    }

    /**
     * Only valid with a date/deleted field
     */
    protected function addExistDeleteAllFilter($filters=[], $filter_name , $active='active',$deleted='deleted',$all='all')
    {

        if (array_key_exists( $filter_name , $filters))
        {
            switch($filters[$filter_name])
            {
                case $active:
                    $this->addFilter($filter_name,'where','deleted', NULL );
                    break;
                case $deleted:
                    $this->addFilter($filter_name,'where','deleted !=', 'NULL' );
                    break;   
                case $all:                    
                default:
                    //do nothing
                    break;                                                             
            }
        }

        return true;
    }


    protected function clearFilter()
    {
        $this->_filter = [];
    }

    protected function getFilter()
    {
        return $this->_filter;
    }


    public function count_all()
    {
        $filter = [];
        $count = $this->count_by($filter);
        return $count;
    }    

}