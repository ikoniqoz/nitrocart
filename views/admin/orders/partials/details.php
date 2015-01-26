<style>
	table.darker td
	{
		color:#555;
	}
</style>
	<fieldset>
			<table class='darker'>
				<tbody>
					<tr>
						<td colspan='2'>
							<h3><?php echo lang('nitrocart:orders:identification'); ?></h3>
						</td>
					</tr>
					<tr>
						<?php if ($order->user_id && $customer): ?>
							<td>
								<?php echo lang('nitrocart:orders:customer'); ?>:
							</td>
							<td>
								<?php echo anchor('user/' . $customer->id, $customer->display_name, array('class'=>'')); ?>

								| <a target='new' class='button' href='{{x:uri x='ADMIN'}}/customers/edit/<?php echo $customer->id; ?>'>View Customer File</a>
							</td>
						<?php else: ?>
							<td>
								<label><?php echo lang('nitrocart:orders:customer'); ?>:
							</td>
							<td>
								{{$customer.display_name}} (Guest)
							</td>
						<?php endif; ?>
					</tr>
					<tr>
						
							<td>
								Member or Guest
							</td>
							<td>
								<?php echo user_displaygroup($order->user_id); ?>
								
							</td>
					</tr>					
					<tr>
						<td>
							<?php echo lang('nitrocart:orders:ip_address'); ?>
						</td>
						<td>
							 <strong>{{order.ip_address}}</strong>
						</td>
					</tr>




					<?php if( $this->config->item('admin/orders/show_totals_on_details') ) : ?> 
					<tr>
						<td colspan='2'>
							<h3>Totals</h3>
						</td>
					</tr>

					<?php if( $this->config->item('admin/orders/show_points') ) : ?> 
					<tr>
						<td>
							User accumulated points
						</td>
						<td>
							<span style='color:#447'><?php echo $order->total_points; ?> (PTS)</span>
						</td>
					</tr>
					<?php endif;?>
					<tr>
						<td>
							<?php echo lang('nitrocart:orders:shipping_amount'); ?>
						</td>
						<td>
							<span style='font-family:courier'><?php echo nc_format_price($order->total_shipping); ?></span>
						</td>
					</tr>					
					<tr>
						<td>
							Total <?php echo lang('nitrocart:orders:discounts'); ?></label>
						</td>
						<td>
							<span style='font-family:courier'><?php echo nc_format_price($order->total_discount); ?></span>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('nitrocart:orders:total_tax'); ?>
						</td>
						<td>
							<span style='font-family:courier'><?php echo nc_format_price($order->total_tax ); ?></span>
						</td>
					</tr>					
					<tr>
						<td>
							Order Subtotal (tax excl)
						</td>
						<td>
							<span style='font-family:courier'><?php echo nc_format_price( $order->total_subtotal ); ?></span>							
						</td>
					</tr>
					<tr>
						<td>
							Order total (tax inc)
						</td>
						<td>
							<strong style='font-family:courier'><?php echo nc_format_price($order->total_totals); ?></strong>
						</td>
					</tr>
					<?php endif;?>
					<?php if( $this->config->item('admin/order/details/show_checkoutmethod') ) : ?>
					<tr>
						<td colspan='2'>
							<h3><?php echo lang('nitrocart:orders:pmt_shipping'); ?></h3>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('nitrocart:orders:date_order_placed'); ?>
						</td>
						<td>
							 <strong> <?php echo date('d / M / Y ', $order->order_date); ?> </strong> @ <?php echo date('H:i:s',$order->order_date) ;?> <small><em>{ <?php echo timespan($order->order_date); ?> <?php echo lang('nitrocart:orders:ago'); ?> }</em></small>
						</td>
					</tr>
					<?php if(isset( $order->paid_date)) : ?>
					<tr>
						<td>
							Paid Date
						</td>
						<td>
							 <strong> <?php echo date('d / M / Y ', $order->paid_date); ?> </strong> @ <?php echo date('H:i:s',$order->paid_date) ;?> <small><em>{ <?php echo timespan($order->paid_date); ?> <?php echo lang('nitrocart:orders:ago'); ?> }</em></small>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td>
							<?php echo lang('nitrocart:orders:payment_method'); ?>
						</td>
						<td>
							{{payments.link}}
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang('nitrocart:orders:shipping_options'); ?> 
						</td>
						<td>
							{{shipping_method.link}}
						</td>
					</tr>
					<?php endif;?>					
				</tbody>
			</table>

		</fieldset>