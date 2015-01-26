<?php namespace Nitrocart\Ui\Presenters;

use \Nitro\Ui\Presenters\CorePresenter;

/**
 * @author Sal Bordonaro
 */
class AdminProductPresenter extends \Nitro\Ui\Presenters\CorePresenter
{

	public function __construct($config)
	{
		parent::__construct();
		$this->show_product_other_tab = $config->config->item('admin/show_product_other_tab');
		$this->mod_path = $config->mod_path;

		$this->template
			->enable_parser(true)           
			->set('base_amount_pricing', NC_BASEPRICING )
            ->append_js('nitrocart::admin/plugins/buttons.js')
            ->append_js('nitrocart::admin/util.js')
            ->append_css('nitrocart::admin/admin.css')
            ->append_css('nitrocart::admin/stags.css')
            ->append_css('nitrocart::admin/tables.css')
            ->append_css('nitrocart::admin/deprecated.css')
            ->append_css('nitrocart::admin/buttons/buttons.css')
            ->append_css('nitrocart::admin/buttons/font-awesome.min.css')
			->append_metadata('<script></script>')
			->append_metadata('<script type="text/javascript">' . "\n  var MOD_PATH = '" . $this->mod_path . "';" . "\n</script>");
	}

	public function variant($product)
	{
		$this->template->title($this->module_details['name'], lang('nitrocart:products:edit'));
		$this->template->enable_parser(true);
		$this->template->set_layout(false);
		$this->default_view = 'admin/products/partials/variant';
		$this->present($product);	
	}

	public function single($product, $mode = 'view')
	{
		$this->template->title($this->module_details['name'], lang('nitrocart:products:edit'))
				->set('show_product_other_tab',$this->show_product_other_tab)
				->set('userDisplayMode',$mode)
				->set('mode',$mode)
				->append_js('nitrocart::admin/products.js')
				->append_js('nitrocart::admin/common.js');

		$this->default_view = 'admin/products/edit';
		$this->present($product);		
	}

	public function create($defaults)
	{
		$this->template->title($this->module_details['name'], lang('nitrocart:products:create'))
				->append_js('nitrocart::admin/products.js')
				->append_js('nitrocart::admin/common.js');
		$this->default_view = 'admin/products/create';
		$this->present($defaults);		
	}


	/*
	 * Static functions below
	 *
	 *
	 *
	 */


	/**
	 * @param $options Array 	- Categories to create product with specific category setup
	 *						 	- ['categories'=> [1,2,3],... ]
	 */
	static public function GetDropdownOptions( & $object_handle, $options=[] )
	{
		$ci = get_instance();

		$ci->load->model('nitrocart/admin/packages_admin_m');
		$ci->load->model('nitrocart/admin/packages_groups_admin_m');
		$ci->load->model('nitrocart/admin/products_types_admin_m');
		$ci->load->model('nitrocart/zones_m');

		$object_handle->available_packages = $ci->packages_admin_m->get_for_admin();
		$object_handle->available_groups = $ci->packages_groups_admin_m->get_for_admin();
		$object_handle->available_taxes = $ci->tax_m->get_admin_select();
		$object_handle->available_types = $ci->products_types_admin_m->get_for_admin();
		$object_handle->available_zones = $ci->zones_m->get_for_admin();

		$object_handle->default_taxID = $ci->tax_admin_m->getDefaultID();
		$object_handle->default_typeID = $ci->products_types_admin_m->getDefaultID(); 
		$object_handle->default_groupID = $ci->packages_groups_admin_m->getDefaultID();
		$object_handle->default_zone_ID = $ci->zones_m->getDefaultID();	
	}

}