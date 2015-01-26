
			<fieldset>
				<table>
					<tr>
						<td><?php echo lang('nitrocart:orders:email'); ?></td>
						<td><?php echo $shipping_address->email; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('nitrocart:orders:first_name'); ?></td>
						<td><?php echo $shipping_address->first_name; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('nitrocart:orders:last_name'); ?></td>
						<td><?php echo $shipping_address->last_name; ?></td>
					</tr>
					<?php if ($shipping_address->company != ""): ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:company'); ?></td>
						<td><?php echo $shipping_address->company; ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:address'); ?></td>
						<td><?php echo $shipping_address->address1; ?>,<?php echo $shipping_address->address2; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('nitrocart:orders:city'); ?></td>
						<td><?php echo $shipping_address->city; ?></td>
					</tr>
					<?php if ($shipping_address->state != ""): ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:state'); ?></td>
						<td><?php echo $shipping_address->state; ?></td>
					</tr>
					<?php endif; ?>
					<?php if ($shipping_address->country != ""): ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:country'); ?></td>
						<td><?php echo $shipping_address->country; ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:zip'); ?></td>
						<td><?php echo $shipping_address->zip; ?></td>
					</tr>			
					<tr>
						<td><?php echo lang('nitrocart:orders:phone'); ?></td>
						<td><?php echo $shipping_address->phone; ?></td>
					</tr>	
				</table>
			</fieldset>			