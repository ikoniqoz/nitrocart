

	<?php foreach ($orders as $order) : ?>
		<tr>
			<td><?php echo '#'. (0 + $order->id); ?></td>


			<td  style="width:35px" >
				<?php echo gravatar($order->customer_email);?>
				<a href="{{x:uri x='ADMIN'}}/orders/order/<?php echo $order->id; ?>"><div class='img_customer_del'></div></a>
			</td>

			<td>
				<?php echo anchor(NC_ADMIN_ROUTE.'/orders/order/' . $order->id, $order->customer_name, array('class'=>'nc_links',  'title' => lang('nitrocart:common:view') ) ); ?>
			</td>

			<td class="collapse">
				<?php echo format_date($order->order_date); ?>
			</td>

			<td class="collapse">
				<?php echo nc_format_price($order->total_totals); ?>
			</td>
			<td>
				<?php $clashop_name = Helper_order_paid_status_color('',$order->paid_date); ?>
				<div class='stags <?php echo $clashop_name;?>'><?php echo ($order->paid_date==NULL)? 'Un Paid' : 'Paid';?></div>
			</td>
			<td>
				<?php $clashop_name = Helper_order_status_color($order->status_id); ?>
				<div class='tooltip-s stags <?php echo $clashop_name;?>' title='<?php echo strtoupper($order->status);?>'><?php echo strtoupper($order->status);?></div>
				<?php if($order->deleted!=NULL):?>
					<span class='stags red'><?php echo lang('nitrocart:orders:deleted');?></span>
				<?php endif;?>
			</td>

			<td>
				<span style="float:right;">
						<?php if(($order->paid_date==NULL)&& ($order->deleted==NULL)):?>
							<a href="<?php echo NC_ADMIN_ROUTE.'/orders/reinvoice/' . $order->id;?>" class="btn orange"> Send Invoice</a>
						<?php endif;?>
						<a href="<?php echo NC_ADMIN_ROUTE.'/orders/order/' . $order->id;?>" class="btn blue"> <?php echo lang('nitrocart:orders:view'); ?></a>
				</span>
			</td>

		</tr>
	<?php endforeach;?>
	<tr>
		<td colspan='10'>			
			<div class="inner" style="float:right;">
					<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
			</div>		
		</td>						
	</tr>

<script>
	tooltip_reset();
</script>

