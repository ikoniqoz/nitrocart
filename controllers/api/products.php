<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
include_once( dirname(__FILE__) .'./../core/api_core.php');
class Products extends Api_core
{

	protected $section = 'api';

	public function __construct()
	{
		parent::__construct();            
	}

	/**
	 * Gain access to all products
	 * 
	 * @param  [type]  $key    [description]
	 * @param  [type]  $action [description]
	 * @param  integer $limit  [description]
	 * @param  integer $offset [description]
	 * @return [type]          [description]
	 */
	public function listall($key,$limit=50,$offset=0)
	{
		$endpoint = 'api/'.__METHOD__.'/';

		//if not valid,  the reqponse will die, no need to check after this line
		parent::req($endpoint,$key);

		//ok now go
		$result = $this->db->where('deleted',NULL)->where('public',1)->limit($limit)->offset($offset)->get('nct_products')->result();


		//send back the data
		parent::send($endpoint,JSONStatus::Success,'',$result);
	}




	public function featured($key,$limit=50,$offset=0)
	{
		$endpoint = 'api/'.__METHOD__.'/';

		//if not valid,  the reqponse will die, no need to check after this line
		parent::req($endpoint,$key);

		//ok now go
		$result = $this->db->where('deleted',NULL)->where('public',1)->where('featured',1)->limit($limit)->offset($offset)->get('nct_products')->result();


		//send back the data
		parent::send($endpoint,JSONStatus::Success,'',$result);
	}


	/**
	 * [product description]
	 * @param  string $action [description]
	 * @param  [type] $id     [description]
	 * @param  [type] $key    [description]
	 * @return [type]         [description]
	 */
	public function product($id,$key)
	{
		//$endpoint = "api/products/product/'.$action.'/ [{$id}]";		
		$endpoint = "api/".__METHOD__."/ [{$id}]";	
		//if not valid,  the reqponse will die, no need to check after this line
		parent::req($endpoint,$key);

		// default values
		$message ='';
		$result = [];
		$status = JSONStatus::Success;

		$result = $this->db->where('deleted',NULL)->where('id',$id)->where('public',1)->get('nct_products')->row();

		// Handle no result return
		if(!$result){
			$status =JSONStatus::Error;
			$message ='Unable to find product.';
		}

		//send back the data
		parent::send($endpoint,$status,$message,$result);
	}


	/**
	 * Get list of variations
	 * @param  [type] $id  [description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function variations($id,$key)
	{
		$endpoint = "api/".__METHOD__."/ [{$id}]";	

		//if not valid,  the reqponse will die, no need to check after this line
		parent::req($endpoint,$key);

		// default values
		$message ='';
		$result = [];
		$status =JSONStatus::Success;

		// sql
		$result = $this->db
				->select('nct_products_variances.*')
				->join('nct_products', 'nct_products.id = nct_products_variances.product_id')
				->where('nct_products_variances.product_id',$id)
				->where('nct_products_variances.available',1)
				->where('nct_products.public',1)
				->where('nct_products.deleted',NULL)	
				->get('nct_products_variances')->result();

		// Handle no result return
		if(!$result){
			$status =JSONStatus::Error;
			$message ='Unable to find variation.';
		}

		//send back the data
		parent::send($endpoint,$status,$message,$result);
	}
	
}
