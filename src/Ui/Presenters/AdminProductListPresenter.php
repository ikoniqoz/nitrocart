<?php namespace Nitrocart\Ui\Presenters;

use \Nitro\Ui\Presenters\CorePresenter;

/**
 * @author Sal Bordonaro
 */
class AdminProductListPresenter extends \Nitro\Ui\Presenters\CorePresenter
{

	public function __construct($config)
	{
		parent::__construct();
		$this->show_product_other_tab = $config->config->item('admin/show_product_other_tab');
		$this->mod_path = $config->mod_path;

		$this->mod_name = $this->module_details['name'];

        $this->template
        			->set('base_amount_pricing',NC_BASEPRICING)         
					->append_js('nitrocart::admin/products.js')
					->append_js('nitrocart::admin/common.js')  
					->append_metadata('<script type="text/javascript">' . "\n  var MOD_PATH = '" . $this->mod_path . "';" . "\n</script>")	          
					->enable_parser(true);		
	}

	public function present($object=null)
	{
		$this->template->title($this->mod_name, lang('nitrocart:products:edit'))
				->set('show_product_other_tab',$this->show_product_other_tab)
				->set('userDisplayMode',$mode)
				->set('mode',$mode)
				->append_js('nitrocart::admin/products.js')
				->append_js('nitrocart::admin/common.js');

		$this->default_view = 'admin/products/edit';

		parent::present($object);
	}

	public function initCall($data,$filter_type)
	{
		// Build the view with ROUTE/views/admin/products.php
		$this->template->title($this->mod_name)
					->set('filter_type',$filter_type)
                    ->append_js('nitrocart::admin/plugins/buttons.js')
					->append_js('admin/filter.js')                
                    ->append_js('nitrocart::admin/util.js')
                    ->append_css('nitrocart::admin/admin.css')
                    ->append_css('nitrocart::admin/stags.css')
                    ->append_css('nitrocart::admin/tables.css')
                    ->append_css('nitrocart::admin/pagination.css')
                    ->append_css('nitrocart::admin/deprecated.css')
                    ->append_css('nitrocart::admin/buttons/buttons.css')
                    ->append_css('nitrocart::admin/buttons/font-awesome.min.css')
					->build('admin/products/products', $data);		
	}


	public function callback_build($data)
	{
		$this->template
				->set_layout(false)
				->set('pagination', $data->pagination)
				->build('admin/products/line_item',$data);
	}	

}