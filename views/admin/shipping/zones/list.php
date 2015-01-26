<div class="one_full" id="">
	<section class="title">
		    <span>
				<h4>Shipping Zones</h4>
			</span>
	</section>
	<section class="item">
		<div class="content">
	
				<?php if (!empty($zones)): ?>

					<table>
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Default</th>
								<th>
									<span style='float:right'>
										Actions
									</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($zones as $zone): ?>
								<tr>
									<td><?php echo $zone->id; ?></td>
									<td><?php echo $zone->name; ?></td>
									<td><?php echo yesNoBOOL($zone->default, 'string', '<span style="color:green">Yes</span>','<span style="color:gray">No</span>'); ?></td>

									
									<td class="actions">
											<a class='button orange' href="{{x:uri x='ADMIN'}}/shipping_zones/countries/<?php echo $zone->id;?>">Countries</a>
											<a class='button blue' href="{{x:uri x='ADMIN'}}/shipping_zones/edit/<?php echo $zone->id;?>">Edit</a>
											<a class='button red confirm' href="{{x:uri x='ADMIN'}}/shipping_zones/delete/<?php echo $zone->id;?>">Delete</a>
									</td>
									

								</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5">
									<div class="inner"></div>
								</td>
							</tr>
						</tfoot>
					</table>

				<?php else: ?>
					<div class="no_data">No Shipping Zones to display.</div>
				<?php endif; ?>
				


		</div>
	</section>
</div>
