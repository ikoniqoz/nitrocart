<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
class Products_options_library extends ViewObject
{

	public function __construct($params = [])
	{
		parent::__construct();
		$this->load->library('settings/settings');
		log_message('debug', "Products Library Class Initialized");
	}


	public function process($product_id, $css_select,$css_option,$id_select)
	{

		//now add our own css/selectors
		$css_select = $css_select . ' eav_oi_select';
		$css_option = $css_option . '';
		$id_select = $id_select . '';


		//
		// Get a list of variances that are disabled
		//
		$disabled = $this->_get_disable_variances($product_id);




		//
		// Get the template values
		//
		$options = $this->_get_template_values($product_id);





		//var_dump($options);die;

		foreach($options as $key =>$option)
		{
			

			$option = (array) $option;	


			$options[$key] = 
			[ 
				'attribute'		=> strtolower($option['e_label']),
				'label'			=> $option['e_label'],
				'value'			=> '',
				'values'		=> [],
				'select_start' 	=> "<select class='{$css_select}' data-option-id='{$option['id']}' id='{$id_select}' name='form_eav_{$option['id']}'>",
				'select_end'   	=> "</select>",
				'count'			=> 0,
				'hidden'		=> '',
				'is_hidden'		=> false,
				'disabled'		=> '',
			];


			$this->db
					->select('e_value,e_label')
					->distinct()
					->where('e_product',$product_id)
					->where('e_variance !=', 'NULL')
					->where('e_label',$option['e_label']);

			if(count($disabled)>0)
			{
				$this->db->where_not_in('e_variance', $disabled);
			}


			$theoptions = $this->db->get('nct_e_attributes')->result();


			//var_dump($theoptions);die;

			//note the number of options
			$options[$key]['count'] = count($theoptions);				

			foreach($theoptions as $somekey => $aoption)
			{
				$aoption = (array) $aoption;

				//if we see a blank value lets skip as if it doesnt exist!
				/*
				if(trim($aoption['e_value']) =='')
				{
					unset($theoptions[$somekey]);
					continue;
				}
				*/

				$a = []; 
				
				
				if($options[$key]['count'] == 1)
				{
					$options[$key]['hidden'] = "<input type='hidden' name='form_eav_{$option['id']}' value='{$aoption['e_value']}'>";
					$options[$key]['value'] = $aoption['e_value'];
					$options[$key]['select_start'] = '';
					$options[$key]['select_end'] = '';
					$options[$key]['label'] = '';
					$options[$key]['disabled'] = '';
					$options[$key]['is_hidden'] = true;
					$a['option'] = '';
				}

				if($options[$key]['count'] > 1)
				{
					$a['option'] = "<option class='{$css_option}' value='{$aoption['e_value']}' {$options[$key]['disabled']} >{$aoption['e_value']}</option>";
				}

				$options[$key]['values'][] = $a;
			}
		}

		return $options;
	}


	private function _get_disable_variances($product_id)
	{


		//
		// Set up a disabled option array
		//
		$disabled = [];


		// Get all variances of a product_id WHERE available = false. Then
		// add to the diabled array 
		$dis_variances = $this->db->where('available',0)->where('product_id',$product_id)->get('nct_products_variances')->result();
		

		//
		// We could also (based on admin choice) disable the option if the variance qty available is === 0
		//


		//add to the index
		foreach($dis_variances as $disabled_variance)
		{
			$disabled[$disabled_variance->id] = $disabled_variance->id;
		}



		return $disabled;
	}


	private function _get_template_values($product_id)
	{
		$options = $this->db
			->where('e_product',$product_id)
			->where('e_variance',NULL)
			->where('e_value IS NOT NULL', null, false)
			->distinct()
			->get('nct_e_attributes')
			->result();

		return $options;		
	}


}
// END Cart Class