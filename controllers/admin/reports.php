<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Reports extends Admin_Controller
{

	protected $section = 'reports';
	private $data;

	private $reportNames = array();

	public function __construct()
	{

		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		role_or_die('nitrocart', 'admin_reports');

		system_installed_or_die('feature_reports');
        $this->lang->load('nitrocart/nitrocart_admin_reports');
		$this->load->model('nitrocart/reports_m');
		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ADMIN_ROUTE ;
		$this->data = new ViewObject();
		$this->load->library('nitrocart/reports_library');

        $this->template
                    ->append_js('nitrocart::admin/plugins/buttons.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css');
	}


	/**
	 * List all items
	 */
	public function index()
	{
		$data = new ViewObject();
		$reports = $this->db->where('enabled',1)->order_by('type','asc')->order_by('ordering_count','asc')->get('nct_reports')->result();

		//$data->allreports = $this->reports_library->get_all_report_types();
		//Events::trigger('SHOPEVT_AdminReportListGet', $data);

		$this->template
				->enable_parser(true)
				->title($this->module_details['name'])
				->set('reports',$reports)
				->build('admin/reports/list');
	}


	/**
	 * include_extra = depending on the report will be include _delete  or include zero based results
	 *	for date, note that month is first
	 * $p2='01/01/2014', $p3='1/30/2014')
	 */
	public function view($reportName='mostviewed')
	{
		//set default variables
		$data =array();

		$day = '01/01/2000';
		$date_start = date("Y-m-d", strtotime($day)); 
		$date_end 	= date('Y-m-d');

		// Build page
		$this->template
			->title($this->module_details['name'])
			->set('reportdata',$data)
			->set('date_start',$date_start)
			->set('date_end',$date_end)
			->build('admin/reports/view/'.$reportName);	
	}	

	/**
	 * This type of report required date params
	 */
	/*public function daterange($reportName='mostviewed', $limit=10, $include_extra=false, $p2='', $p3='',$do_download=false)*/	
	public function daterange($reportName='mostviewed')
	{
		//default data
		$limit = 10;
		$include_extra = false;
			$day = '01/01/2000';
			$date_start = date("Y-m-d", strtotime($day)); 
			$date_end 	= date('Y-m-d');
		$p2	= $date_start;
		$p3	= $date_end;
		$do_download = false;


		//override values from posting
		if($input = $this->input->post())
		{
			$reportName = $input['reportname'];
			$limit = $input['limit'];
			$include_extra = $input['include_extra'];
			$p2	= $input['date_start'];
			$p3	= $input['date_end'];

			$do_download = ($input['btnAction']=='Download')?true:false;
		}


		$data = array();
		$data = $this->reports_m->$reportName($limit,$include_extra,$p2,$p3);
		if($do_download)
		{
			$this->load->helper('download');
			$this->load->library('format');
			$report_type = 'csv';

			force_download(
    			$reportName.'.'.$report_type,
    			$this->format->factory($data)->{'to_'.$report_type}()
    			);
		}
		else
		{
			// Build page
			$this->template
				->title($this->module_details['name'])
				->set('reportdata',$data)
				->set('date_start',$p2)
				->set('date_end',$p3)
				->build('admin/reports/view/'.$reportName);

		}		
	}	

	public function allorders($reportName='mostviewed', $limit=10, $include_extra=false, $p2='', $p3='',$do_download=false)
	{
		if($input = $this->input->post())
		{
			$reportName = $input['reportname'];
			$limit = $input['limit'];
			$include_extra = $input['include_extra'];

			$do_download = ($input['btnAction']=='Download')?true:false;
		}

		$data = array();

		$data = $this->reports_m->$reportName($limit,$include_extra,$p2,$p3);

		if($do_download)
		{
			$this->load->helper('download');
			$this->load->library('format');
			$report_type = 'csv';

			force_download(
    			$reportName.'.'.$report_type,
    			$this->format->factory($data)->{'to_'.$report_type}()
    			);
		}
		else
		{
			// Build page
			$this->template
				->title($this->module_details['name'])
				->set('reportdata',$data)
				->set('date_start',$p2)
				->set('date_end',$p3)
				->set('limit',$limit)
				->build('admin/reports/view/'.$reportName);

		}		
	}	

	/**
	 *
	 * @param  string  $reportName  [description]
	 * @param  integer $last        [description]
	 * @param  boolean $second_view ome reports have a secondary view, if set to true and the report supports it you will receive the same report with alterant columns, if the report does not support it then you will get the standard view, either 1 or 0 is accepted
	 * @param  string  $type        [description]
	 * @return [type]               [description]
	 */
	public function export($reportName='mostviewed', $last=5, $second_view=0, $type='csv' )
	{

		$this->load->helper('download');
		$this->load->library('format');

		// Get name of the report
		$report_name = $reportName;

		$report_type = 'csv';


		$data = $this->reports_m->$reportName(10,$second_view);

		// Force the download and do not navigate elsewhere
    	force_download(
    			$report_name.'.'.$report_type,
    			$this->format->factory($data)->{'to_'.$report_type}()
    			);
	}


}