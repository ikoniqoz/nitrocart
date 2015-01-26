<?php

	$items = array();

	$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/view/{$id}", false,  "View", false, "eye-open");

	if(group_has_role('nitrocart', 'admin_r_catalogue_edit'))
	{
		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/duplicate/{$id}/list", true,  "Copy", false, "copy","","");
		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/product/duplicate/{$id}/edit", false,  "Copy and Edit", false, "copy");
	}

	$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/products", 	true,  "Refresh", false, "refresh");

	echo dropdownMenuList($items,lang('nitrocart:products:actions'));
