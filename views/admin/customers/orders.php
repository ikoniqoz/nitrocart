<section class="title">
	<h4><a href='{{x:uri x='ADMIN'}}/customers/'>[Back &larr;] </a> <?php echo lang('nitrocart:customers:client_order');?>: {{customer.last_name}}, {{customer.first_name}}</h4>
</section>
<section class="item">
	<div class="content">
		<?php echo form_open(NC_ADMIN_ROUTE.'/customers'); ?>
		<table>
			{{orders}}
				<tr>
					<td>{{id}}</td>
					<td>{{ helper:date format="m/d/Y" timestamp=order_date }}</td>
					<td>{{status}}</td>
					<td>{{total_amount_order_wt}}</td>
					<td>{{if deleted==''}}{{else}}
						<span class='stags red'>
								<?php echo lang('nitrocart:customers:deleted');?>
						</span>
						{{endif}}
					</td>
					<td>
						<span style='float:right'>
							<a href='{{x:uri x='ADMIN'}}/orders/order/{{id}}/0' class='button green modal'><?php echo lang('nitrocart:admin:view');?></a>
							<a href='{{x:uri x='ADMIN'}}/orders/order/{{id}}' class='button blue'><?php echo lang('nitrocart:customers:manage');?></a>
						</span>
					</td>
				</tr>
			{{/orders}}
		</table>
		{{pagination:links}}
		<?php echo form_close(); ?>

		<br /><p>
		<a class='btn blue' href='{{x:uri x='ADMIN'}}/customers/'>Back to Customers</a>

	</div>
</section>