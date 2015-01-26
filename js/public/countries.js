/*
 * NitroCart Public Countries
 *
 */

$(function() {

    $(document).on('change', '.nc_country_select', function(event) 
    {
        var country_id = $(this).val();
        var api =  SITE_URL + 'api/countries/country/' + country_id + '/states';
        $.post( api ).done(function(data)
        {
            var obj = jQuery.parseJSON(data);
            //if(obj.status == 'success')
            //{
                //repopulate the list
                $('.nc_states_select').html(obj.html)
            //}
        });

        // Prevent Navigation
        event.preventDefault();

      });      

});