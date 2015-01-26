<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
/**
 * Build admin dropdown menu
 *
 * @param  [type]  $url     [description]
 * @param  [type]  $item_id [description]
 * @param  boolean $edit    [description]
 * @param  boolean $delete  [description]
 * @param  boolean $view    [description]
 * @return [type]           [description]
 */
function dropdownMenu($url, $item_id,  $edit=true,$delete=true,$view=false)
{
	$items = array();

	if($edit)
		$items[] = dropdownMenuStandard("{$url}/edit/{$item_id}", false, "Edit", false, "edit");

	if($view)
		$items[] = dropdownMenuStandard("{$url}/view/{$item_id}", false, "View", false, "eye-open");

	if($delete)
		$items[] = dropdownMenuStandard("{$url}/delete/{$item_id}", true, "Delete", true, "minus");

	return dropdownMenuList($items, 'Actions');

}

function dropdownMenuList($items, $actionsText='More',$rounded=false)
{
	$actionsText='More';
	$str  = '<span class="sbtn-dropdown" data-buttons="dropdown">';
	$rndd = ($rounded)?' sbtn-rounded ' : '' ;
	$str .= "<a href='#'' class='btn {$rndd} orange'>{$actionsText} <i class='icon-caret-down'></i></a>";	
	$str .= '<ul class="sbtn-dropdown">';
	
	foreach($items as $item)
		$str .= $item;

	$str .= '</ul>';
	$str .= '</span>';
	return $str;
}

/**
 *
 * @param  [type]  $url     [description]
 * @param  boolean $divider [description]
 * @param  [type]  $label   [description]
 * @param  boolean $delete  [description]
 * @param  string  $icon    edit | minus | eye-open |
 * @return [type]           [description]
 */
function dropdownMenuStandard($url, $divider=false, $label, $delete=false, $icon='edit',$target='',$class_a='')
{
	$divider = ($divider)? '-divider' : '' ;
	$confirm = ($delete)? 'confirm' : '' ;

	if($delete)
	{
		$delete = 'delete';
		$class_a = 'confirm';
	}
	else
	{
		$delete = '';
	}

	$target = ($target=='')?'': " target='{$target}' ";

	$str = "<li class='sbtn-dropdown{$divider} {$delete}'><a class='{$class_a}' href='{$url}' $target class='{$confirm}'><i class='icon-{$icon}'></i> {$label}</a></li>";

	return $str;
}

function single_button($url, $divider=false, $label, $delete=false, $icon='edit',$target='')
{
	$divider = ($divider)? '-divider' : '' ;
	$confirm = ($delete)? 'confirm' : '' ;
	$delete = ($delete)? 'delete' : '' ;
	$target = ($target=='')?'': " target='{$target}' ";

	$str = "<a href='{$url}' $target class='sbtn sbtn-rounded orange {$confirm}'><i class='icon-{$icon}'></i> {$label}</a>";

	return $str;
}



/**
 * New API for admin dropdown menus.
 * The new API will have a specific API endpoint per button type rather than a single master function
 */
function nc_MH_STD($text, $url='',$divider=false,$target='new', $extra_class='',$icon='')
{
	return _nc_MH_DDItem($text,$url,$divider,$target, $extra_class,$icon);
}

function nc_MH_EDIT($url='',$divider=false,$target='new', $extra_class='')
{
	return _nc_MH_DDItem('Edit',$url,$divider,$target, $extra_class,'<i class="icon-edit"></i>');
}

function nc_MH_VIEW($url='',$divider=false,$target='new', $extra_class='')
{
	return _nc_MH_DDItem('View',$url,$divider,$target, $extra_class,'<i class="icon-eye-open"></i>');
}

function nc_MH_DELETE($url='',$divider=true,$target='new', $extra_class='')
{
	return _nc_MH_DDItem('Delete',$url,$divider,$target, $extra_class,'<i class="icon-minus"></i>');
}


function _nc_MH_DDItem($string_text,$url='',$divider=false,$target='new', $extra_class='',$icon='')
{
	$target = ($target=='')?'': " target='{$target}' ";
	$divider = ($divider)? '-divider' : '' ;	

	$str = "<li class='sbtn-dropdown{$divider}'><a href='{$url}' $target class='{$extra_class}'>{$icon} {$string_text}</a></li>";
}