<?php

	$items = array();

	//function dropdownMenuStandard($url, $						divider=false, $label, $delete=false, $icon='edit')
	if(group_has_role('nitrocart', 'admin_r_catalogue_edit'))
		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/edit/{$id}", false, lang('nitrocart:products:edit'), false, "edit");

	$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/view/{$id}", false,  lang('nitrocart:products:view'), false, "eye-open");
	$items[] = dropdownMenuStandard(NC_ROUTE."/products/product/{$id}", false,  lang('nitrocart:products:customer_view'), false, "eye-open","new");


	if(group_has_role('nitrocart', 'admin_r_catalogue_edit'))
	{
		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/duplicate/{$id}/list", true,  "Copy", false, "copy","","");
		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/duplicate/{$id}/edit", false,  "Copy and Edit", false, "copy");
	}

	$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/products", 	true,  "Refresh", false, "refresh");

	if(group_has_role('nitrocart', 'admin_r_catalogue_edit'))
		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/delete/{$id}", 	true,  lang('nitrocart:products:delete'), true, "minus");

	echo dropdownMenuList($items,lang('nitrocart:products:actions'));



