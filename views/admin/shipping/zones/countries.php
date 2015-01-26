<div class="one_full" id="">
	<section class="title">
		    <span>
				<h4>Shipping Zone : {{zonename}}</h4>
			</span>
	</section>
	<section class="item">
		<div class="content">

			<div class="one_full" id="">
				<div class='one_third'>
					<?php echo form_open(NC_ADMIN_ROUTE.'/shipping_zones/zoneregion'); ?>
							Enable all countries by region<br/>
							{{regions}}
							<input type='hidden' value='{{zone_id}}' name='zone_id'>							
							<br/>
							<input class='button blue' name='mode' type='submit' value='Add' />
							<input class='button red'  name='mode' type='submit' value='Remove' />					
					<?php echo form_close(); ?>
				</div>
				<div class='one_third last'>
					<?php echo form_open(NC_ADMIN_ROUTE.'/shipping_zones/zone/');?>
					Or; Add a country<br/>
					{{countries}} <br/>
					
					<input type='hidden' value='{{zone_id}}' name='zone_id'>

					<input type='submit' value='Add' class='button blue'>
				
					<?php echo form_close();?>
				</div>
				<span style='float:right'>
		

				</span>						
			</div>

			<div class="one_full" id="">	

							<h3>Assigned Countries</h3>

							<?php if(count ($zoned_countries)): ?>
							<br />
							<table class=''>
								<thead>
									<tr>
										<th><?php echo lang('nitrocart:shipping:id'); ?></th>
										<th><?php echo lang('nitrocart:shipping:name'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($zoned_countries as $item): ?>
										<tr>
											<td><?php echo $item->id; ?></td>
											<td><?php echo $item->country_t; ?></td>
											<td class="actions">
												<a class='button red' href="{{x:uri x='ADMIN'}}/shipping_zones/remove_country_zone_assignment/<?php echo $item->zone_id;?>/<?php echo $item->country_id;?>">Remove</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>


							<?php else: ?>
									<div class="no_data">No countries are in this zone</div>
							<?php endif; ?>


					

							<div class="inner" style="">
									<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
							</div>		

				</div>


		</div>
	</section>
</div>



<script>

	function link_shipping_zone_glob(button, is_linked, row_id)
	{

		var toggle = (is_linked)? '0' : '1' ;
		var buttonText = ((is_linked)? 'Active' : 'Inactive');
		var classes = ((is_linked)? 'orange' :  'gray');
		var url = '<?php echo NC_ADMIN_ROUTE;?>/shipping_zones/country/'+toggle+'/' + row_id;

		button.text(buttonText);
		button.attr('href', url );
		button.attr('class','glob_linker button '+classes);

	}


    $(document).on('click', '.glob_linker', function(event) {

		var url = $(this).attr('href');
		var button = $(this);

		$.post(url).done(function(data)
		{
			var obj = jQuery.parseJSON(data);
			if(obj.status == 'success')
			{
					link_shipping_zone_glob(  button , obj.is_linked, obj.id);
			}
		});

		// Prevent Navigation
		event.preventDefault();
    });

  




  	function link_shipping_zone(button, is_linked, country_id)
	{

		var toggle = (is_linked)? '0' : '1' ;
		var buttonText = ((is_linked)? 'Active' : 'Inactive');
		var classes = ((is_linked)? 'orange' :  'gray');
		var url = '<?php echo NC_ADMIN_ROUTE;?>/shipping_zones/assign_country_to_zone/{{zone_id}}/'+country_id;

		button.text(buttonText);
		button.attr('href', url );
		button.attr('class','glob_linker button '+classes);

	}


    $(document).on('click', '.zone_linker', function(event) {

		var url = $(this).attr('href');
		var button = $(this);

		$.post(url).done(function(data)
		{
			var obj = jQuery.parseJSON(data);
			if(obj.status == 'success')
			{
					link_shipping_zone(  button , obj.is_linked, obj.id);
			}
		});

		// Prevent Navigation
		event.preventDefault();
    });

</script>