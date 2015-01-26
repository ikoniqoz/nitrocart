			<fieldset>
				<table>
					<tr>
						<td><?php echo lang('nitrocart:orders:email'); ?></td>
						<td><?php echo $invoice->email; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('nitrocart:orders:first_name'); ?></td>
						<td><?php echo $invoice->first_name; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('nitrocart:orders:last_name'); ?></td>
						<td><?php echo $invoice->last_name; ?></td>
					</tr>
					<?php if ($invoice->company != ""): ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:company'); ?></td>
						<td><?php echo $invoice->company; ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:address'); ?></td>
						<td><?php echo $invoice->address1; ?>,<?php echo $invoice->address2; ?></td>
					</tr>
					<tr>
						<td><?php echo lang('nitrocart:orders:city'); ?></td>
						<td><?php echo $invoice->city; ?></td>
					</tr>
					<?php if ($invoice->state != ""): ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:state'); ?></td>
						<td><?php echo $invoice->state; ?></td>
					</tr>
					<?php endif; ?>
					<?php if ($invoice->country != ""): ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:country'); ?></td>
						<td><?php echo $invoice->country; ?></td>
					</tr>
					<?php endif; ?>
					<tr>
						<td><?php echo lang('nitrocart:orders:zip'); ?></td>
						<td><?php echo $invoice->zip; ?></td>
					</tr>			
					<tr>
						<td><?php echo lang('nitrocart:orders:phone'); ?></td>
						<td><?php echo $invoice->phone; ?></td>
					</tr>	
				</table>
			</fieldset>