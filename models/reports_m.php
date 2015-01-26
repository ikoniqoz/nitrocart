<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Reports_m extends MY_Model {


    public function __construct()
    {
        parent::__construct();
    }




    /**
     * Get listing of products by what has been sold
     *
     *  - Req (Date range)
     *  - This reports all products and the total of revenue of the product within the date range provided
     *
     *  $date_start='01/01/2001',$date_end='01/12/2001'
     */
    public function prodsold($limit = 5, $inc_deleted=false, $date_start='',$date_end='', $as_most_sold=false)
    {
        //check for null date values
        if($date_start =='')
            $date_start = date('Y-m-d H:i:s', strtotime("now")); //1 month after date start;

        if($date_end == '')
            $date_end = date('Y-m-d H:i:s', strtotime("+1 month")); //1 month after date start;


        $date_start     = strtotime( $date_start ); //'01/01/2001';
        $date_end       = strtotime( $date_end ); //'01/12/2001'
        $date_start     = date("Y-m-d H:i:s",$date_start);
        $date_end       = date("Y-m-d H:i:s",$date_end);

       
        //sum the total amount
        $this->db->select('product_id,variant_id,title,sum(`qty`) as total_qty, price as item_amount,sum(total) as total_return');
        $this->db->where('created >=', $date_start);
        $this->db->where('created <=', $date_end);  

        //invoice line can store shipping and other info
        //so dont report these.
        $this->db->where('product_id !=', 'NULL');  
        $this->db->where('variant_id !=', 'NULL');  


        if($as_most_sold)
            $this->db->order_by('total_qty', 'desc');


        //this is the default action
        if($inc_deleted==false)
            $this->db->where('deleted', NULL);


        if($as_most_sold)
        {
            $this->db->group_by("variant_id", false);
        }
        else
        {
            $this->db->group_by(array("product_id", "variant_id"), false);
        }



        //$this->db->order_by('qty desc', false); //order by date placed desc
        $this->db->limit($limit);

        $result = $this->db->get('nct_order_invoice')->result();
        $stats = [];
        foreach ($result as $item)
        {
            $stats[] = (array) $item ;
        }
        return $stats;
    }

    /**
     * Most sold product
     *
     * @param  integer $limit      [description]
     * @param  boolean $ommit_zero [description]
     * @return [type]              [description]
     */
    public function mostsoldp($limit = 5, $inc_deleted=false, $date_start='',$date_end='')
    {
        return $this->prodsold( $limit, $inc_deleted, $date_start, $date_end, true);
    }




    public function highorders($limit = 5,$inc_deleted=false, $x='',$y='')
    {
        $this->db->select('id, user_id, order_date, total_totals,count_items,deleted');
        $this->db->order_by("total_totals desc", false);        
        $this->db->limit($limit);
        $result = $this->db->get('nct_orders')->result();

        $stats = [];
        foreach ($result as $item)
        {
            $stats[] = (array) $item ;
        }

        return $stats;
    }

    /**
     *
     * Show the most viewed product (of all time)
     *
     */
    public function mostviewed($limit = 50,$inc_zero=false, $x='',$y='')
    {

        if($inc_zero)
        {
            //do donthing
        }
        else
        {
            $this->db->where('views >', 0);
        }


        $this->db->group_by('id', false);
        $this->db->order_by('views desc', false);
        $this->db->limit($limit);
        $result = $this->db->get('nct_products')->result();

        $stats = [];

        foreach ($result as $item)
        {
            $stats[] = (array) $item ;            
        }

        return $stats;
    }







    public function recentorders($limit = 5,$inc_deleted=false, $x='',$y='')
    {
        $this->db->select('id, user_id, order_date, total_totals,count_items,deleted');
        $this->db->order_by('order_date desc', false);
        $this->db->limit($limit);
        $result = $this->db->get('nct_orders')->result();

        $stats = [];
        foreach ($result as $item)
        {
            $stats[] = (array) $item ;    
        }
        return $stats;
    }

    /**
     * Orders by a date range
     */
    public function orderbydate($limit = 5,$inc_deleted=false, $date_start='01/01/2001',$date_end='01/12/2001')
    {
        //first convert the date format
        $date_start = strtotime( $date_start ); //'01/01/2001';
        $date_end = strtotime( $date_end ); //'01/12/2001'


        $this->db->select('id, user_id, order_date, total_amount_order_wt,total_count_items,deleted');
        $this->db->where('order_date >=', $date_start);
        $this->db->where('order_date <=', $date_end);
        $this->db->order_by('order_date desc', false);
        $this->db->limit($limit);
        $result = $this->db->get('nct_orders')->result();

        $stats = [];

        foreach ($result as $item)
        {
            $stats[] = (array) $item ;  
        }

        return $stats;
    }  

    public function orderbydategroup($limit = 5,$inc_deleted=false, $date_start='01/01/2001',$date_end='01/12/2001')
    {
        //first convert the date format
        $date_start = strtotime( $date_start ); //'01/01/2001';
        $date_end = strtotime( $date_end ); //'01/12/2001'


        $date_start = date("Y-m-d H:i:s",$date_start);
        $date_end = date("Y-m-d H:i:s",$date_end);


        $this->db->select('order_date,created, count(*) as order_sales, sum(total_amount_order_wt) as orders_total');
        $this->db->where('created >=', $date_start);
        $this->db->where('created <=', $date_end);
        $this->db->order_by('created desc', false);
        $this->db->group_by('created');
        $this->db->limit($limit);
        $result = $this->db->get('nct_orders')->result();

        $stats = [];
        foreach ($result as $item)
        {
            $stats[] = (array) $item ;    
        }

        return $stats;
    }  


    /**
     * this is not yet finished, weneed to group by customer_id which will be on the order table
     */
    public function bestcustomers($limit = 5,$inc_deleted=false, $date_start='',$date_end='')
    {
        $this->db->select('user_id,count(*) as order_sales, sum(count_items) as total_items, sum(total_totals) as orders_total');
        $this->db->order_by('orders_total desc', false);
        $this->db->group_by('user_id');
        $this->db->limit($limit);
        $result = $this->db->get('nct_orders')->result();
        $stats = [];
        foreach ($result as $item)
        {
            $stats[] = (array) $item ;    
        }
        return $stats;
    }  
}