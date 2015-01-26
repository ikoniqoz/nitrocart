

$(function() {

	/**
	 * 
	 * eav_oi_select == EAV_OPTION_ITEM_SELECT
	 *
	 * 1. Get the form ID/NAME
	 *
	 * 2. GET ALL FORM VALUES
	 * 3. POST REQUEST of FORM
	 *
	 */
    $(document).on('change', '.eav_oi_select', function(event) 
    {
		var data_option = $(this).attr('data-option-id');
        var form_id = $(this).attr('form-id');

        form_id = '#'+ form_id;

        alert('hello');

        $(form_id).submit(function(ev) 
        {

            var $form = $(this);

            $.ajax($form.attr('action'), 
            {
                type: $form.attr('method') || 'get',
                data: $form.serialize(),

            }).then(function() 
            {
                alert('success');

            }, function() 
            {
                alert('error');
            });

            //event.preventDefault();
        });


		event.preventDefault();
    }); 







    $('.ajax_store_remove_cart_item').click( function() {

        rowid = $(this).attr('rowid');
        postto = SITE_URL +  NC_ROUTE + '/cart/delete/' + rowid;
        table_tr = 'tr#' + rowid;

        $.post( postto ).done(function(data)
        {
            var ndata = jQuery.parseJSON(data);
            if(ndata.status=='error')
            {
                ajax_error(ndata.message);
            }
            else
            {
                //update_cart_info(ndata.cost,ndata.qty);
                $(table_tr).fadeOut('slow');
                ajax_success(ndata.message);
            }
        });

        return false;
    });


});