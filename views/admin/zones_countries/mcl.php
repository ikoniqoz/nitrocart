<div class="one_full" id="">
	<section class="title">
		    <span>
				<h4>Shipping Zones</h4>
			</span>
	</section>
	<section class="item">
		<div class="content">

			<div class="one_full" id="">

				<div class='one_third'>
					<?php echo form_open(NC_ADMIN_ROUTE.'/shipping_zones/toggle'); ?>
							Enable all countries in this region<br/>
							{{regions}}
							<br/>
							<input class='button blue' name='mode' type='submit' value='Add' />
							<input class='button red'  name='mode' type='submit' value='Remove' />
					<?php echo form_close(); ?>
				</div>
				
			</div>
			<div class="one_full" id="">			


							<table>
								<thead>
									<tr>
										<th><?php echo lang('nitrocart:shipping:id'); ?></th>
										<th><?php echo lang('nitrocart:shipping:name'); ?></th>
										<th><?php echo lang('nitrocart:shipping:code'); ?></th>
										<th><?php echo lang('nitrocart:shipping:region'); ?></th>
										<th>Status</th>
										<th><?php echo lang('nitrocart:shipping:enabled'); ?></th>
									</tr>
								</thead>
								<tbody>
									{{countries}}
										<tr>
											<td>{{id}}</td>
											<td>{{name}}</td>
											<td>{{code2}}</td>
											<td>{{region}}</td>
											<td>
												<span id='mcl_status_{{id}}'>
													<i class='icon-custom_status_{{enabled}}'></i>	
												</span>
											</td>
											<td class="actions">
												<a class='zone_linker button blue' data-id='{{id}}'>Toggle</a>
												<a class='button blue' href="{{x:uri x='ADMIN'}}/states/bycountry/{{id}}/?filter-states=1&f-country_id={{id}}">Edit States</a>
											</td>
										</tr>
									{{/countries}}
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6">
											<div class="inner">{{pagination}}</div>
										</td>
									</tr>
								</tfoot>
							</table>
				</div>


		</div>
	</section>
</div>


<style>
.icon-custom_status_0:before{content:"\f00d";color:#aaa;}
.icon-custom_status_1:before{content:"\f00c";color:#55aa55;}
</style>
<script>




    $(document).on('click', '.zone_linker', function(event) {

    	var id = $(this).attr('data-id');
		var url = '<?php echo NC_ADMIN_ROUTE;?>/shipping_zones/country_toggle/' + id;

		$.post(url).done(function(data)
		{
			var obj = jQuery.parseJSON(data);
			if(obj.status == 'success')
			{
				var str = "<i class='icon-custom_status_"+obj.int_status+"'></i>";
				$( '#mcl_status_' + id ).html( str );
			}

		});

		// Prevent Navigation
		event.preventDefault();
    });

  

</script>