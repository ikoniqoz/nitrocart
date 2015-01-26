
function setfield(i,idstring)
{

	var id = "#nitrocart_col_" + idstring + "_" + i;

	var ct = parseInt(  $(id).attr("status") );

	ct = (ct==1)?0:1;

	var url_array = Array();

	url_array[1] = 'nitrocart/admin/products/setfield/'+i+'/searchable/' + ct;	
	url_array[2] = 'nitrocart/admin/products/setfield/'+i+'/public/' + ct;
	url_array[3] = 'nitrocart/admin/products/setfield/'+i+'/featured/' + ct;
	
	the_url = url_array[idstring];

	$.post( the_url ).done(function(data)
	{
		var obj = jQuery.parseJSON(data);

		switch(obj.status)
		{
			case 'success':
				if(obj.prop==0)
				{
                	$(id).attr("class", 'tooltip-s icon-minus');
					$(id).attr("status", 0);
					break;
				}
				else if(obj.prop==1)
				{			
                	$(id).attr("class", 'tooltip-s icon-ok');
					$(id).attr("status", 1);
					break;
				}
			case 'error':
			default:
				alert('Oops, something went wrong. Try refreshing the page');
                $(id).attr("class", 'icon-warning');
				$(id).attr("status", 0);
				break;
		}

	});
}

/**	
 * Hide/Show filter section
 *
 */
function toggle_filter()
{
	var e = document.getElementById('hideable_filters');
    var o = document.getElementById('flink');

	if (e.style.display =='none')
    {
        e.style.display = 'block';
    }
    else
    {
        e.style.display = 'none';
    }
}
