<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/ 
class Api_core extends Public_Controller 
{

	protected $section = 'api';
	protected $subsection = 'public';

	public function __construct()
	{
		parent::__construct();
        Events::trigger('SHOPEVT_ShopPublicController');
        $this->load->library('nitrocart/Toolbox/Nc_status');
        $this->load->library('nitrocart/Toolbox/Nc_string');  

        ci()->current_user = $this->current_user = $this->ion_auth->get_user();

        /**
         * Check to see if the system is instaled
         */
        if(! (system_installed('feature_api')) AND ($this->db->table_exists('nct_api_keys') ))
        {
        	echo "Sorry.. the system is unavailable at thi time.";die;
        }
        
        
        /**
         * Track user request usage
         * @var boolean
         */
        $this->track_request =true;

        /**
         * Note that track-request needs to be enabled for this to work
         * @var boolean
         */
        $this->log_responses = true;     

	}

	protected function do_auth()
	{
		$x = $this->input->post();
		$r = $this->ion_auth->login($x['email'], $x['password']);
		if($r)
		{
			$user = $this->ion_auth->get_user_by_email($x['email']);

			$this->session->set_userdata('user_id',$user->id);
		 	$this->ion_auth->force_login($user->id, 1);
		}

		return $r;
	}

	/**
	 * public test of Auth
	 */
	public function auth()
	{
		$r = $this->do_auth();
		$x['status'] = $r;
		echo json_encode($x);die;
	}

	public function user()
	{
		//$user = ci()->current_user = $this->current_user = $this->ion_auth->get_user( $this->session->userdata('user_id') ); // $this->current_user ? $this->current_user : $this->ion_auth->get_user();

		$user = $this->current_user ? $this->current_user : $this->ion_auth->get_user();

		echo json_encode($user);die;
	}	


	/**
	 * Public handler
	 * @return [type] [description]
	 */
	public function index()
	{
		if($input = $this->input->post())
		{
			var_dump($input);
		}
		die;
	}



	/**
	 * All incoming request should be routed/extend this.
	 * 
	 * @param  [type] $key    [description]
	 * @return [type]         [description]
	 */
	protected function req($endpoint,$key)
	{
		if($row = $this->_validate_key($endpoint,$key))
		{
			if($this->track_request) 
				$this->_logrequest($row);

			return true;
		}
		else
		{
			// Just die now, no need to bubble
			$array = ['status'=>false,'message'=>'Sorry, you do not have access.'];
			echo json_encode($array);die;
		}
	}


	/**
	 * Send the data
	 * @param  [type] $endpoint [description]
	 * @param  [type] $status   [description]
	 * @param  [type] $message  [description]
	 * @param  [type] $result   [description]
	 * @return [type]           [description]
	 */
	protected function send($endpoint,$status,$message,$result)
	{
		$return = [];
		$return['status'] = $status;
		$return['message'] = $message;
		$return['result'] = $result;

		$response = json_encode($return);

		if(($this->log_responses)AND($this->track_request)) 
			$this->log($endpoint,$response);

		die($response);		
		//die('<code>'.$response.'</code>');		
	}


	/**
	 * Validates a key and acccess rights
	 * 
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	private function _validate_key($endpoint,$key)
	{
		if($row = $this->db->where('key',strtoupper($key))->get('nct_api_keys')->row())
		{
			if($row->max_allowed > $row->tot_curr_requests)
			{
				$this->key_id = $row->id;
				if($row->enabled)
				{
					return $row;
				}
			}
		}
		return false;
	}


	private function _logrequest($keyrow)
	{
		$data =  ['tot_requests'=>($keyrow->tot_requests + 1),'tot_curr_requests'=>($keyrow->tot_curr_requests + 1)];

		$this->db
			->where('key',strtoupper($keyrow->key))
			->update('nct_api_keys', $data );
	}

	/**
	 * Log information in database
	 * 
	 * @param  [type] $endpoint [description]
	 * @param  [type] $response [description]
	 * @return [type]           [description]
	 */
	private function log($endpoint,$response)
	{
		$data =
		[
			'key_id'	=> $this->key_id,
			'endpoint'	=> $endpoint,
			'date'		=> date('Y-m-d H:m:s'),
			'result'	=> $response
		];
		$this->db->insert('nct_api_requests',$data);
	}

}
