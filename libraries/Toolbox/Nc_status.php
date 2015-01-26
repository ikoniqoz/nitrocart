<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Nc_status
{
}
trait NCStatusable
{
 	public function __invoke($x='')
    {
    	//var_dump($x);
        echo $this->_message;
    }	
    public function __toString()
    {
        return (string) $this->_message;
    }	
	public function getStatus()
	{
		return $this->_status;
	}
	public function getMessage()
	{
		return $this->_message;
	}	
	public function setStatus($new_status,$new_message=NULL)
	{
		$new_message = ($new_message==NULL)?$this->_message:$new_message;
		return $this->update($new_status,$new_message);
	}
	public function setMessage($new_message)
	{
		if(!is_string($new_message)) return false;		
		return $this->_message = $new_message;
	}  
	private function update($new_status,$new_message)
	{
		if(!is_bool($new_status)) return false;			
		if(!is_string($new_message)) return false;
		$this->_status = $new_status;		
		$this->_message = $new_message;
		return true;
	}  	 
}
trait NCMultiConstructParam
{
	public function __construct() 
	{
        //initialize
        $this::_init();
        if(func_num_args()>0)
        {
        	if (method_exists($this,$f='__construct_'.func_num_args())) 
        	{
            	call_user_func_array(array($this,$f),func_get_args());
        	} 	
        }
	} 
}
class NCMessageObject
{
	private $_status;
	private $_message;

	use NCMultiConstructParam;
	use NCStatusable;

	private function _init() 
	{
		$this->_status = true;
		$this->_message = '';
		$this->extra_param = [];
    }     
	private function __construct_1($status) 
	{
		$this->_status = $status;
    } 
	private function __construct_2($status, $message) 
	{
		$this->_status = $status;
		$this->_message = $message;		
    } 
    public function set($paramname,$paramvalue)
    {
    	$this->extra_param[] = [$paramname, $paramvalue];
    }
	public function asJson()
	{
		$ret_array = [];
		$ret_array['status'] = $this->_status;
		$ret_array['message'] = $this->_message;
		return json_encode($ret_array);
	}

	/**
	 * Similar to asJson, asJsonResult is expected to be sent back to the client
	 * browser and thus opts for string literals of 'success' or 'error' 
	 * instead of true:false
	 * @return [type] [description]
	 */
	public function asJsonReturn()
	{
		$ret_array = [];
		$ret_array['status'] = ($this->_status)?'success':'error';
		$ret_array['message'] = $this->_message;

		foreach($this->extra_param as $key => $param)
		{
			$ret_array[$param[0]]=$param[1];
		}
		return json_encode($ret_array);
	} 

	/**
	 * This will take the asJsonReturn string and return to the browser
	 * @return [type] [description]
	 */
	public function sendJson()
	{
		echo die($this->asJsonReturn());
	}	
}
