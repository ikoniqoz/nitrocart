//
//
// This file is for any JS required just for the Orders admin section
// No other logic should be included in this file
//
//
function toggle_filter()
{
	var e = document.getElementById('hideable_filters');
    //var e = document.getElementById('filters_group');
    var o = document.getElementById('flink');

	if (e.style.display =='none')
    {
        /*o.className  = 'img_icon_title img_filter selected';*/
        e.style.display = 'block';
    }
    else
    {
        /*o.className  = 'img_icon_title img_filter';*/
        e.style.display = 'none';
    }

}