<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
require_once(dirname(__FILE__) . '/core_admin_filter_m.php');
class Products_admin_filter_m extends Core_admin_filter_m
{


    private static $__FILT_ALL           = 'all';
    private static $__FILT_TYPE          = 'producttype';
    private static $__FILT_PRICE         = 'price';
    private static $__FILT_NOPRICE       = 'noprice';

    public function __construct()
    {
        parent::__construct();
    }


    protected function _prepare_filter($filter = [])
    {
        $this->clearFilter();

        //noprice
        if (array_key_exists('f_filter', $filter))
        {   
            $arg = $this->getArgs($filter['f_filter']);//or die;

            switch( $arg[self::FILTER_TYPE] )
            {
                case self::$__FILT_ALL: 
                    {
                        //code for all products
                    }  
                    break;  
                case self::$__FILT_TYPE;
                    {
                        $this->addFilter('f_filter','where','type_id',((int) $arg[self::FILTER_VALUE] ));
                    }
                    break;
                    
                case self::$__FILT_PRICE;
                    {
                        $this->addFilter('__lookup__','price','price',  1 );
                    }
                    break;  
                case self::$__FILT_NOPRICE;
                    {
                        $this->addFilter('__lookup__','noprice','noprice', 0 );                        
                    }
                    break;  
                     
            }
            
        }
        
        $this->addStringExistFilter($filter, 'search', 'like','name' );

        $this->addYesNoAllFilter($filter, 'f_featured' , 'featured');

        $this->addExistDeleteAllFilter( $filter, 'status' );

        $this->addOnOffAllFilter($filter, 'visibility' ,'public', 'yes', 'no', 'all');

        $this->setOrderBy($filter);

        return $this->getFilter();
    }


    /**
     * Admin Count Filter
     *
     * @param  array  $filter [description]
     * @return [type]         [description]
     */
    public function filter_count($filter = [])
    {
        $this->reset_query();

        $new_filters = $this->_prepare_filter($filter);
     

        //noprice
        $x = $this->_checkVariances($new_filters);  

        if($x===false) return 0;


        //where+like
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if(($action=='where')|| ($action=='like'))
                $this->$action($key,$value);
        }

        //order bys
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if($action=='order_by')
                $this->$action($key,$value);
        }      


        $this->from($this->_table);


        return $this->count_all_results();
    }


    public function filter($filter = [] , $limit, $offset = 0)
    {
        $this->reset_query();

        $new_filters = $this->_prepare_filter($filter);

        //noprice
        $x = $this->_checkVariances($new_filters);  

        if($x===false) return [];

        //where+like
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if(($action=='where')|| ($action=='like'))
                $this->$action($key,$value);
        }

        //order bys
        foreach($new_filters as $filter)
        {
            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            if($action=='order_by')
                $this->$action($key,$value);
        }      

        $this->limit( $limit , $offset );

        return $this->get_all();
    }


    protected function _checkVariances($new_filters=[])
    {
        if( isset( $new_filters['__lookup__']) )
        {
            $filter = $new_filters['__lookup__'];

            $action = $filter['action'];
            $key = $filter['key'];
            $value = $filter['value'];
            
            
            if( ( $action =='noprice' ) OR ( $action == 'price' ) )
            {

                    $result = $this->db->where('deleted',NULL)->group_by('product_id')->get('nct_products_variances')->result();
                    $ids = [];
                    foreach($result as $k=>$v)
                    {
                        $ids[] = $v->product_id;
                    }

                    if(count($ids))
                    {
                        //needs price records
                        switch( $action )
                        {
                            case 'price':
                                $this->where_in('id',  $ids);      
                                break;
                            case 'noprice':
                                $this->where_not_in('id',  $ids);   
                                break;                                
                        }

                        return true;

                    }    
                    else 
                    {
                        return false;
                    }                  
            }

        }
          
 
        return true;
    }

}