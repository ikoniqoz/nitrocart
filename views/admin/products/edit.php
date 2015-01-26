<input type="hidden" name="static_product_id" id="static_product_id" data-pid="{{id}}" />

<section class="title">
    <span>
        <h4>
            <em><?php echo lang('nitrocart:products:product');?></em>: <span id='title_product_name'>{{name}}</span>
        </h4>
    </span>
</section>


<section class="item">

    <div class="content">

        <?php if( $this->config->item('admin/product/show_infobar') ) : ?> 
            <?php $this->load->view('admin/products/partials/infobar'); ?>
        <?php endif;?>


        <input type="hidden" name="id" id="id" value="{{id}}" />

        <div class="tabs">
            <!-- Here we create the tabs -->
            <ul class="tab-menu">
                <li><a href="#product-tab"><span><?php echo lang('nitrocart:products:product');?></span></a></li>
                <li><a href="#price-tab"><?php echo lang('nitrocart:products:variations');?><span></span></a></li>
                <?php
                    //This is better that hard coding, becuase modules can be registered and auto populated
                    foreach($module_tabs as $module)
                    {
                        //write tab HTML here
                        echo "<li><a class='tab-loader'  data-load='".strtolower($module->name)."' href='#".strtolower($module->name)."-tab'><span>".$module->name."</span></a></li>";
                    }
                ?>

                <?php if(system_installed('feature_affiliates')):?>
                        <li><a href="#affiliates-tab">Affiliates<span></span></a></li>
                <?php endif;?>


                {{if show_product_other_tab}}
                    <li><a href="#extra-tab">Other<span></span></a></li>
                {{endif}}
            </ul>

            <div class="form_inputs" id="product-tab">
                <?php $this->load->view("admin/products/partials/{$mode}/product"); ?>
            </div>
            <div class="form_inputs" id="price-tab">
                <?php $this->load->view("admin/products/partials/{$mode}/price"); ?>
            </div>

            <?php foreach($module_tabs as $module) :?>
                    <div class='form_inputs' id='<?php echo strtolower($module->name); ?>-tab'>
                        <?php $this->load->view( strtolower($module->namespace) . '/admin/products/partials/tab' );?>
                    </div>
            <?php endforeach;  ?>

            <?php if(system_installed('feature_affiliates')):?>
                <div class="form_inputs" id="affiliates-tab">
                    <?php $this->load->view("admin/affiliates/product_tab"); ?>
                </div>
            <?php endif;?>

            {{if show_product_other_tab}}
                <div class="form_inputs" id="extra-tab">
                    <?php $this->load->view("admin/products/partials/{$mode}/extra"); ?>
                </div>
            {{endif}}


            

        </div>

    </div>

</section>

<script>
/*  
 * Build the price_record line (variance)0 for the variation tab.
 *
 */
function pr_get_str(thevariance)
{
    if(thevariance.sku =='')
    {
        thevariance.sku = "<a href='{{x:uri x='ADMIN'}}/variances/edit/"+ thevariance.id +"' class='modal'>SET A CODE</a>";
    }

    thevariance.sk_on_hand = 'N/A'; 
    var str  ="<tr pr-id='"+thevariance.id+"'>";
    str += "<td><a var-id='"+thevariance.id+"' class='button view_variance_button' href='{{x:uri x='ADMIN'}}/product/variant/"+thevariance.id+"'>"+thevariance.id+"</a></td>";
    str += "<td> <a href='{{x:uri x='ADMIN'}}/variances/edit/"+ thevariance.id +"' class='modal'>"+ thevariance.sku +"</a></td>";
    str += "<td><a>"+ thevariance.sk_on_hand +"</a></td>";

    <?php if($base_amount_pricing == true ):?>
        str += "<td> <a href='{{x:uri x='ADMIN'}}/variances/price/get/"+ thevariance.id +"' class='modal tooltip-s' title='Price'> <?php echo nc_currency_symbol();?> "+ thevariance.base +" </a></td>";     
    <?php endif;?>
    
    str += "<td> <a href='{{x:uri x='ADMIN'}}/variances/price/get/"+ thevariance.id +"' class='modal tooltip-s' title='Price'> <?php echo nc_currency_symbol();?> "+ thevariance.price +" </a></td>";

    str += "<td> <a class='call_toggle_pr ' func='edit_available' href='{{x:uri x='ADMIN'}}/variances/toggle_value/"+ thevariance.id +"'>"+ thevariance.available +"</a></td>";
    str += "<td> <a class='call_toggle_pr ' func='edit_discountable' href='{{x:uri x='ADMIN'}}/variances/toggle_value/"+ thevariance.id +"'>"+ thevariance.discountable +"</a></td>";
    str += "<td> <a class='call_toggle_pr ' func='toggle_shippable' href='{{x:uri x='ADMIN'}}/variances/toggle_value/"+ thevariance.id +"'>"+ thevariance.is_shippable +"</a></td>";

    str += "<td>";
    str += "        <span style='float:right'>";
    str += "            <a href='{{x:uri x='ADMIN'}}/variances/duplicate_aj/"+ thevariance.id +"' class='copyVariantAJ button green tooltip-s' title='Copy'><i class='icon-copy'></i></a>";
    str += "            <a href='{{x:uri x='ADMIN'}}/attributes/ajax_get/<?php echo $id;?>/"+ thevariance.id +"' class='button blue modal tooltip-s' title='Attributes'><i class='icon-star'></i></a>";     
    str += "            <a pr-id='"+ thevariance.id +"' href='#' class='delPriceRecord button red delete_button tooltip-s' title='Delete'>&times;</a></td>";
    str += "        </span>";           
    str += "</tr>";

    return str;     
}



/*
 * Add a price record(variation) to 
 * the table of variations
 */
function addPriceRecord(obj)
{
    $('table.prices_list tbody').append( formatPriceRecord( obj ) );
}

function updatePriceRecord(obj)
{
    $('table.prices_list tbody tr[pr-id="'+obj.id+'"]').replaceWith( formatPriceRecord(obj) );
}

function formatPriceRecord(obj)
{
    obj.available =(obj.available == 1)? 'Yes' : 'No';  
    obj.discountable =(obj.discountable == 1)? 'Yes' : 'No';            
    obj.is_shippable =(obj.is_shippable == 1)? 'Yes' : 'No';
    return pr_get_str(obj) ;
}


function validate(h,w,l,weight)
{
    if(h<=0) return false;

    if(w<=0) return false;

    if(l<=0) return false;

    return true;
}

function get_Senddata()
{
    /* The record to update*/
    var var_name = $("input[name='var_name']").val();
    var var_sku = $("input[name='var_sku']").val();

    return {
            name:var_name,
            sku:var_sku,
          };

}

function close_all_variats()
{
    $('.vcal').html('<h2>Variant Details { [None Selected] } </h2><br/>Please select a variation.');
}

$(function() {

    $(document).on('click', '.call_toggle_pr', function(event) {
        var url = $(this).attr("href");
        var _func = $(this).attr("func");

        var senddata = { dummy:'dummy', func:_func };
        $.post(url, senddata ).done(function(data)
        {
            var obj = jQuery.parseJSON(data);
            $('.VarianceContext').html(obj.message);
            if(obj.status == 'success')
            {
                close_all_variats();
                updatePriceRecord(obj.record);
                tooltip_reset();
            }
        });
        event.preventDefault();
    });

    $(document).on('click', '.copyVariantAJ', function(event) {
            var url = $(this).attr('href');
            $.post( url ).done(function(data)
            {
                var obj = jQuery.parseJSON(data);
                if(obj.status == 'success')
                {
                    close_all_variats();
                    addPriceRecord(obj.record);
                    tooltip_reset();
                }
            });
            event.preventDefault();
    }); 

    $(document).on('click', '.editVariantName', function(event) {

            var var_id = $("input[name='editvariance_id']").val();
            var var_name = $("input[name='var_name']").val();

            var url = "<?php echo NC_ADMIN_ROUTE;?>/variances/set_name/"+var_id;

            var senddata = {
                            id:var_id,
                            name:var_name,
                          };

            $.post(url, senddata ).done(function(data)
            {
                var obj = jQuery.parseJSON(data);
                $('.VarianceContext').html(obj.message);
                if(obj.status == 'success')
                {
                    close_all_variats();
                    updatePriceRecord(obj.record);
                    tooltip_reset();
                }
            });

            // Prevent Navigation
            event.preventDefault();
    });



    $(document).on('click', '.view_variance_button', function(event) {

            var var_id = $(this).attr('var-id');
            var url = $(this).attr('href');

            var ContentArea = '#VarianceContentArea_List';
            //clear all first
            close_all_variats();
            //$(ContentArea).html('cool');
            $(ContentArea).load(url);
            //$(ContentArea2).load(url2);

            // Prevent Navigation
            event.preventDefault();
    });


    $(document).on('click', '.clear_all_variance_data', function(event) {
            //clear all first
            close_all_variats();
            // Prevent Navigation
            event.preventDefault();
    });


    $(document).on('click', '.editPriceData', function(event) {

            var var_id = $("input[name='editvariance_id']").val();

            var new_discountable = $("select[name='discountable']").val();
            var new_price = $("input[name='price']").val();
            var new_base = $("input[name='base']").val();
            var new_rrp = $("input[name='rrp']").val();
            
            var url = "<?php echo NC_ADMIN_ROUTE;?>/variances/price/update/"+var_id;

            var senddata = {
                            discountable:new_discountable,
                            price:new_price,
                            base:new_base,
                            rrp:new_rrp,
                          };

            $.post(url, senddata ).done(function(data)
            {
                var obj = jQuery.parseJSON(data);
                $('.VarianceContext').html(obj.message);

                if(obj.status == 'success')
                {
                    close_all_variats();
                    updatePriceRecord(obj.record);
                    tooltip_reset();
                }

            });

            // Prevent Navigation
            event.preventDefault();
    });

    $(document).on('click', '.editShippingData', function(event) {

            var var_id = $("input[name='editvariance_id']").val();
            var select_pkg_group_id = $("select[name='pkg_group_id']").val();
            var zid = $("select[name='zone_id']").val();
            var input_height = $("input[name='height']").val();
            var input_width = $("input[name='width']").val();
            var input_length = $("input[name='length']").val();
            var input_weight = $("input[name='weight']").val();

            if(validate(input_height,input_width,input_length,input_weight)===false)
            {
                alert("There is an error with the Height, Width, Weight, please check your data.");
                return;
            }

            var url = "<?php echo NC_ADMIN_ROUTE;?>/variances/shipping/update/"+var_id;

            var senddata = {
                            pkg_group_id:select_pkg_group_id,
                            height:input_height,
                            width:input_width,
                            length:input_length,
                            weight:input_weight,
                            zone_id:zid
                          };

            $.post(url, senddata ).done(function(data)
            {
                var obj = jQuery.parseJSON(data);
                //send message regardless of status
                $('.VarianceContext').html(obj.message);

                if(obj.status == 'success')
                {
                    //no need to display/update visuals
                }   

            });

            // Prevent Navigation
            event.preventDefault();
    });

    $(document).on('click', '.editVarianceRecord', function(event) {

            var var_id = $("input[name='editvariance_id']").val();

            /* The record to update*/
            var senddata =  get_Senddata();
            senddata.id = var_id;
            senddata.product_id = <?php echo $id;?>;

            var url = "<?php echo NC_ADMIN_ROUTE;?>/variances/edit/"+var_id;

            $.post(url, senddata ).done(function(data)
            {
                var obj = jQuery.parseJSON(data);

                //send message regardless of status
                $('.VarianceContext').html(obj.message);

                if(obj.status == 'success')
                {
                    close_all_variats();
                    updatePriceRecord(obj.record);
                    tooltip_reset();
                }

            });

            // Prevent Navigation
            event.preventDefault();
    });

    $(document).on('click', '.addVarianceRecord', function(event) {

            var senddata =  get_Senddata();

            var url = "<?php echo NC_ADMIN_ROUTE;?>/variances/create/<?php echo $id;?>/";

            $.post(url, senddata ).done(function(data)
            {

                var obj = jQuery.parseJSON(data);

                $('.VarianceContext').html(obj.message);

                if(obj.status == 'success')
                {
                    close_all_variats();
                    addPriceRecord(obj.record);
                    tooltip_reset();
                }

            });

            // Prevent Navigation
            event.preventDefault();
    });

    $(document).on('click', '.delPriceRecord', function(event) {

           var r = confirm("Are you sure you want to delete this.");

            if (r == true)
            {
                var con = confirm()
                var record_id = $(this).attr('pr-id');
                var item = $('table.prices_list tbody tr[pr-id="'+record_id+'"]');
                var url = "<?php echo NC_ADMIN_ROUTE;?>/variances/delete/" + record_id;

                $.post(url).done(function(data)
                {
                    close_all_variats();
                    var obj = jQuery.parseJSON(data);

                    if(obj.status == 'success')
                    {
                        item.ncRemoveLine(3000);
                    }
                    else
                    {
                        alert(obj.status);
                    }

                    tooltip_reset();
                });

            }

            // Prevent Navigation
            event.preventDefault();
    });
});
</script>