<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Dashboard extends Admin_Controller
{
	// Set the section in the UI - Selected Menu
	protected $section = 'dashboard';

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		$this->data = new ViewObject();

		// Load all the Required classes
		$this->load->model('nitrocart/orders_m');

        $this->lang->load('nitrocart/nitrocart_admin_dashboard');


        $this->template
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}


	/**
	 * load the dashboard
	 */
	public function index()
	{

		// Load required Classes
		$this->load->model('nitrocart/admin/products_admin_m');

		$max = 5;

		// Collect 5 most recent orders
		$this->load->model('nitrocart/admin/orders_admin_m');
		$this->data->order_items = $this->orders_admin_m->where('nct_orders.deleted',NULL)->limit($max)->offset(0)->order_by('order_date','desc')->get_all();


		$this->load->model('nitrocart/statistics_m');
        $this->data->cat = $this->statistics_m->get_catalogue_data();

        $this->data->most_viewed =  $this->statistics_m->_get_most_viewed(5);

		//TODO: Use better chart api, also improve the data
		$rows = $this->db->query("select order_date, count(id) as `Val` from ".$this->db->dbprefix('nct_orders')." group by DATE(FROM_UNIXTIME(order_date)) LIMIT 4");
		$this->data->SalesRecords = $rows->result();



 		$this->data->revenue_today = $this->statistics_m->getStoreTotalRevenue();
		$this->data->revenue_today = $this->statistics_m->getStoreRevenue(1);
		$this->data->revenue_monthly = $this->statistics_m->getStoreRevenue(30);
		$this->data->revenue_anual = $this->statistics_m->getStoreRevenue(365);

		//$data->items = & $items;
		$this->template->title($this->module_details['name'])
				->append_js('nitrocart::admin/dashboard.js')
				->append_js('jquery/jquery.flot.js')
				->build('admin/dashboard/dashboard', $this->data);
	}

	/**
	 * api for charting graphs
	 */
    public function stats($chart='orders', $limit = 7)
    {
    	$this->load->model('nitrocart/statistics_m');
        if ($this->input->is_ajax_request())
        {
            $this->data = $this->statistics_m->get_period($limit, $chart );
            echo json_encode($this->data);die;
        }
        //no?... get outta here
        redirect(NC_ROUTE);
    }

}