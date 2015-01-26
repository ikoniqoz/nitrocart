<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Statistics_m extends MY_Model
{

	public $_table = 'nct_products';


	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Get the total revenue of the store for the N number of days from now to in the past
     * return value should be a float.
     */
    public function getStoreRevenue($days=180)
    {

        $dates = [];

        $day_seconds = 86400;
        $period = $days * $day_seconds;

        $this->db->select('SUM(total_totals) AS total', false);
        $this->db->select("FROM_UNIXTIME(`order_date`, '%Y-%m-%d') AS date", false);
        $this->db->where('order_date >', time()-$period);
        $this->db->group_by('date', false);
        $result = $this->db->get('nct_orders')->result();

        //var_dump($result);die;

        $grand = 0;

        foreach ($result as $item)
        {
            $grand += $item->total;
        }
        return $grand;
    }

    /**
     * Get the total revenue of the store of all time.
     * return value should be a float.
     */
    public function getStoreTotalRevenue()
    {
        $dates = [];

        $this->db->select('SUM(total_totals) AS total', false);
        $result = $this->db->get('nct_orders')->row();
        //var_dump($result->total);die;
        return $result->total;
    }


	public function get_catalogue_data()
	{
        $this->load->model('nitrocart/admin/products_admin_m');

		$data['total_products']               = $this->products_admin_m->count_all();
        $data['total_products_live']          = $this->db->where('deleted',NULL)->where('public',1)->count_all_results('nct_products');
        $data['total_products_hidden']        = $this->db->where('deleted',NULL)->where('public',0)->count_all_results('nct_products');
        $data['total_products_live_featured'] = $this->db->where('deleted',NULL)->where('public',1)->where('featured',1)->count_all_results('nct_products');

        $data['total_products_deleted']       = $this->products_admin_m->count_by('deleted !=','``');

        $data['total_shipping_methods']       = $this->db->where('module_type','shipping')->where('enabled',1)->count_all_results('nct_checkout_options');
        $data['total_gateway_methods']       = $this->db->where('module_type','gateway')->where('enabled',1)->count_all_results('nct_checkout_options');

		return $data;
	}


    /**
     *
     * @param  integer $days  [description]
     * @param  string  $chart [order|users|income]
     * @return [type]         [description]
     */
    public function get_period($days = 5, $chart = 'orders')
    {

       $stats = [];

        switch ($chart)
        {

            case 'activity':
                $activity = $this->_get_recent_activity($days);
                $stats[] = ['label' => 'Session', 'data' => $activity];
                break;
            case 'income':
            case 'orders':
            	$orders = $this->_get_orders($days);
                $stats[] = ['label' => 'Orders', 'data' => $orders];
                break;
            case 'unpaid':
            	$orders = $this->_get_unpaid_orders($days);
                $stats[] = ['label' => 'Orders', 'data' => $orders];
                break;
            case 'newusers':
            	$users = $this->_get_new_users($days);
                $stats[] = ['label' => 'Users', 'data' => $users];
                break;
            case 'best':
                //days is actually # of products to get
                $best = $this->_get_best_sellers($days);
                $stats[] = ['label' => 'Sales', 'data' => $best];
                break;
            case 'bestsellers':
                //days is actually # of products to get
                $best = $this->_get_best_sellers($days);
                $stats[] = ['label' => 'Sales', 'data' => $best];
                break;


            case 'views':
                $views = $this->_get_most_viewed($days);
                $stats[] = ['label' => 'Views', 'data' => $views];
                break;
            case 'topclients':
                $top = $this->_get_top_clients($days);
                $stats[] = ['label' => 'Revenue', 'data' => $top];
                break;
            default:
                break;
        }

        return $stats;
    }

    private function _get_orders($days = 7)
    {
    	//$days = 7;
        $dates = [];

        $day_seconds = 86400;
        $period = $days * $day_seconds;

        $this->db->select('COUNT(*) AS total', false);
        $this->db->select("FROM_UNIXTIME(`order_date`, '%Y-%m-%d') AS date", false);
        $this->db->where('order_date >', time()-$period);
        $this->db->group_by('date', false);
        $result = $this->db->get('nct_orders')->result();

        $stats = [];

        for ($index = 0; $index < $days; $index++)
         {
            $timestamp = date('Y-m-d', time() - ($index * $day_seconds));
            $dates[$timestamp] = 0;
        }

        foreach ($result as $item)
        {
            $dates[$item->date] = $item->total;
        }

        $dates = array_reverse($dates);

        foreach ($dates as $key => $value)
        {
            $stats[] = array(strtotime($key)*1000, $value);
        }

        return $stats;
    }

    private function _get_unpaid_orders($days = 7)
    {
    	//$days = 7;
        $dates = [];

        $day_seconds = 86400;
        $period = $days * $day_seconds;

        $this->db->select('COUNT(*) AS total', false);
        $this->db->select("FROM_UNIXTIME(`order_date`, '%Y-%m-%d') AS date", false);
        $this->db->where('order_date >', time()-$period);
		$this->db->where('pmt_status ', 'unpaid');

        $this->db->group_by('date', false);
        $result = $this->db->get('nct_orders')->result();

        $stats = [];

        for ($index = 0; $index < $days; $index++)
         {
            $timestamp = date('Y-m-d', time() - ($index * $day_seconds));
            $dates[$timestamp] = 0;
        }

        foreach ($result as $item)
        {
            $dates[$item->date] = $item->total;
        }

        $dates = array_reverse($dates);

        foreach ($dates as $key => $value)
        {
            $stats[] = array(strtotime($key)*1000, $value);
        }

        return $stats;
    }

    private function _get_top_clients($clients = 7)
    {
        $this->db->select('user_id, sum(cost_items) as total');
        $this->db->group_by('user_id', false);
        $this->db->order_by('total desc', false);
        $this->db->limit($clients);

        $result = $this->db->get('nct_orders')->result();

        $stats = [];
        foreach ($result as $item)
        {
            $user = $this->db->get_where('profiles',['user_id' =>$item->user_id])->result();
            $stats[] =  [$user[0]->first_name,$item->total];
        }
        return $stats;
    }


    private function _get_new_users($days = 7)
    {
    	//$days = 7;
        $dates = [];

        $day_seconds = 86400;
        $period = $days * $day_seconds;

        $this->db->select('COUNT(*) AS total', false);
        $this->db->select("FROM_UNIXTIME(`created_on`, '%Y-%m-%d') AS date", false);
        $this->db->where('created_on >', time()-$period);
        $this->db->group_by('date', false);
        $result = $this->db->get('users')->result();

        $stats = [];

        for ($index = 0; $index < $days; $index++)
         {
            $timestamp = date('Y-m-d', time() - ($index * $day_seconds));
            $dates[$timestamp] = 0;
        }

        foreach ($result as $item)
        {
            $dates[$item->date] = $item->total;
        }

        $dates = array_reverse($dates);

        foreach ($dates as $key => $value)
        {
            $stats[] = array(strtotime($key)*1000, $value);
        }

        return $stats;
    }

    /**
     *
     * SELECT TOP(5) ProductID, SUM(Quantity) AS TotalQuantity
     * FROM order_items
     * GROUP BY ProductID
     * ORDER BY SUM(Quantity) DESC;
     *
     * @param  integer $product_count [description]
     * @return [type]                 [description]
     */
    private function _get_best_sellers($product_count = 5)
    {

        $this->db->select('title, product_id,sum(qty) as total_qty');
        $this->db->group_by('product_id', false);
        $this->db->order_by('sum(qty) desc', false);
        $this->db->limit($product_count);
        $result = $this->db->get('nct_order_invoice')->result();


        $stats = [];
        foreach ($result as $item)
        {
            $stats[] =  [$item->title,$item->total_qty];
        }

        return $stats;
    }

    public function _get_most_viewed($product_count = 5)
    {

        $this->db->select('name, id, views');
        $this->db->where('deleted', NULL);
        $this->db->group_by('id', false);
        $this->db->order_by('views desc', false);
        $this->db->limit($product_count);
        $result = $this->db->get('nct_products')->result();


        $stats = [];

        foreach ($result as $item)
        {
            $stats[] =  [$item->name,$item->views];
        }

        return $stats;
    }



    private function _get_recent_activity($days = 7)
    {
        //$days = 7;
        $dates = [];

        $day_seconds = 86400;
        $period = $days * $day_seconds;

        $this->db->select('COUNT(*) AS total', false);
        $this->db->select("FROM_UNIXTIME(`last_activity`, '%Y-%m-%d') AS date", false);
        $this->db->where('last_activity >', time()-$period);

        $this->db->group_by('date', false);
        $this->db->group_by('ip_address', false);

        $result = $this->db->get('ci_sessions')->result();

        $stats = [];

        for ($index = 0; $index < $days; $index++)
         {
            $timestamp = date('Y-m-d', time() - ($index * $day_seconds));
            $dates[$timestamp] = 0;
        }

        foreach ($result as $item)
        {
            $dates[$item->date] = $item->total;
        }

        $dates = array_reverse($dates);

        foreach ($dates as $key => $value)
        {
            $stats[] = array(strtotime($key)*1000, $value);
        }

        return $stats;
    }

}