<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Affiliates extends Admin_Controller
{

	protected $section = 'affiliates';
	private $data;

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopAdminController');

		role_or_die('nitrocart', 'admin_affiliates');

		$this->refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NC_ROUTE;

		$this->data = new StdClass;

		$this->lang->load('nitrocart/nitrocart_affiliates');
		$this->load->model('nitrocart/features/affiliates_m');


	}

	/**
	 * List all items
	 */
	public function index()
	{
		$affiliates = $this->affiliates_m->get_all();

		$this->template
				->title($this->module_details['name'])
				->set('affiliates',$affiliates)
				->build('admin/affiliates/list');
	}

	/**
	 * Create a new Brand
	 */
	public function create()
	{

		if( $input = $this->input->post() )
		{
			$ok = ($input['name']=='')? false:true;

			$ok OR redirect('admin/nitrocart/affiliates/');

			if($id = $this->affiliates_m->create($input))
			{
				redirect('admin/nitrocart/affiliates/');
			}

		}

		// Build page
		$this->template
			->title($this->module_details['name'])
			->build('admin/affiliates/create');
	}

	public function view($id)
	{

		$affiliates = $this->affiliates_m->get($id);

		// Build page
		$this->template
			->enable_parser(true)
			->title($this->module_details['name'])
			->set('id',$id)
			->set('type',$affiliates)
			->build('admin/affiliates/view');
	}

	public function edit($id)
	{

		if($input = $this->input->post())
		{
			if(isset($input['btnAction']))
			{

				if($this->affiliates_m->update($id,array('name' =>$input['name'])))
				{
					$this->session->set_flashdata('success','affiliate `'.$input['name'].'` updated.');

					if(	$input['btnAction'] == 'save_exit')
						redirect('admin/nitrocart/affiliates/');

					//else
					redirect('admin/nitrocart/affiliates/edit/'.$id);

				}
			}

			$this->session->set_flashdata('error','Invalid operation.');

		}

		$affiliates = $this->affiliates_m->get($id);

		// Build page
		$this->template
			->title($this->module_details['name'])
			->set('id',$id)
			->set('type',$affiliates)
			->build('admin/affiliates/edit');
	}

	public function delete($id)
	{
		$obj = $this->affiliates_m->delete($id);

		$status = ($obj['status']) ? JSONStatus::Success : JSONStatus::Error;

		$this->session->set_flashdata( $status , $obj['message'] );

		// Go back to list
		redirect('admin/nitrocart/affiliates/');
	}


	public function generate()
	{
		if($input = $this->input->post())
		{
			$url = $input['url'];
			$url = ($url=='')? site_url() : $url ;

			$client_code = $input['code'];
			$text_to_display = $input['text'];
			$text_to_display = ($text_to_display=='')? 'Click here' : $text_to_display ;
			$q_static = 'QUANTAM';

			echo "Link : <a target='new' href='{$url}?{$q_static}={$client_code}'>{$text_to_display}</a> <br />";
			echo "Direct : {$url}?{$q_static}={$client_code}<br/>";
			echo "Link : <textarea readonly=readonly> &lt;a href='{$url}?{$q_static}={$client_code}'&gt;{$text_to_display}&lt;/a&gt; </textarea>";

			die;
		}

	}
	public function get_select_pt()
	{
		$this->load->model('nitrocart/features/affiliates_m');
		$select = $this->affiliates_m->get_for_admin_2();
		//var_dump($select);die;
		echo form_dropdown('client_code',$select);die;
	}
	
	public function generate_pt()
	{
		if($input = $this->input->post())
		{
			$url = $input['url'];
			$url = ($url=='')? site_url() : $url ;

			$client_code = $input['code'];

			if(($client_code==='null') OR ($client_code===null) OR ($client_code===NULL) OR (trim($client_code)==''))
			{
				echo "An invalid code/user was selected.";die;
			}

			$text_to_display = $input['text'];
			$text_to_display = ($text_to_display=='')? 'Click here' : $text_to_display ;
			$q_static = 'QUANTAM';

			echo '<br /><br />';
			echo "TEST : <a target='new' href='{$url}?{$q_static}={$client_code}'>{$text_to_display}</a> <br /><br />";
			echo "URL :<textarea readonly=readonly>{$url}?{$q_static}={$client_code}</textarea> <br />";
			echo "HTML : <textarea readonly=readonly> &lt;a href='{$url}?{$q_static}={$client_code}'&gt;{$text_to_display}&lt;/a&gt; </textarea>";

			die;
		}

	}
}