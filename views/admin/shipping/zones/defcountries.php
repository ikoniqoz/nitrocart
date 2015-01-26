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
							<!--a href='{{x:uri x='ADMIN'}}/shipping_zones/remove_all_Regions' class='btn red confirm'>Clear ALL regions and Countries</a-->
						
					<?php echo form_close(); ?>
				</div>
		
				<span style='float:right'>
					<a href='{{x:uri x='ADMIN'}}/shipping_zones/showall' class='btn green'>Show ALL</a>
						<br/>
						<br/>
					<a href='{{x:uri x='ADMIN'}}/shipping_zones/showactive' class='btn green'>Show Active</a>
					<br />

				</span>
		
			</div>
			<div class="one_full" id="">			
						<?php if (!empty($countries)): ?>

							<table>
								<thead>
									<tr>
										<th><?php echo lang('nitrocart:shipping:id'); ?></th>
										<th><?php echo lang('nitrocart:shipping:name'); ?></th>
										<th><?php echo lang('nitrocart:shipping:code'); ?></th>
										<th><?php echo lang('nitrocart:shipping:region'); ?></th>
										<th><?php echo lang('nitrocart:shipping:enabled'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($countries as $item): ?>
										<tr>
											<td><?php echo $item->id; ?></td>
											<td><?php echo $item->name; ?></td>
											<td><?php echo $item->code2; ?></td>
											<td><?php echo $item->region; ?></td>
											<td class="actions">
												<a class='button blue' href="{{x:uri x='ADMIN'}}/states/bycountry/<?php echo $item->id;?>/?filter-states=1&f-country_id=<?php echo $item->id;?>">Edit States</a>

												<?php if ($item->enabled):?>
													<a class='zone_linker button blue' href="{{x:uri x='ADMIN'}}/shipping_zones/country/0/<?php echo $item->id;?>">Active</a>
												<?php else:?>
													<a class='zone_linker button gray' href="{{x:uri x='ADMIN'}}/shipping_zones/country/1/<?php echo $item->id;?>">Inactive</a>
												<?php endif;?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5">
											<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
										</td>
									</tr>
								</tfoot>
							</table>

						<?php else: ?>
							<div class="no_data">System error, your NitroCart system can no installed countries.</div>
						<?php endif; ?>
				</div>


		</div>
	</section>
</div>



<script>

	function link_shipping_zone(button, is_linked, row_id)
	{

		var toggle = (is_linked)? '0' : '1' ;
		var buttonText = ((is_linked)? 'Active' : 'Inactive');
		var classes = ((is_linked)? 'blue' :  'gray');
		var url = '<?php echo NC_ADMIN_ROUTE;?>/shipping_zones/country/'+toggle+'/' + row_id;

		button.text(buttonText);
		button.attr('href', url );
		button.attr('class','zone_linker button '+classes);

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