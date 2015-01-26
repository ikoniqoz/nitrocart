<section class="title">
	<h4><a href='{{x:uri x='ADMIN'}}/customers/'>[Back &larr;] </a> <?php echo lang('nitrocart:customers:client_order');?>: {{customer.last_name}}, {{customer.first_name}}</h4>
</section>
<section class="item">
	<div class="content">
		<?php echo form_open("{{x:uri x='ADMIN'}}/customers"); ?>
		<table>
			{{addresses}}
				<tr>
					<td>{{id}}</td>
					<td>{{ helper:date format="m/d/Y" timestamp=order_date }}</td>
					<td>{{first_name}}</td>
					<td>{{last_name}}</td>
					<td>{{first_name}}</td>
					<td>{{email}}</td>

					<td>{{country}}</td>
					<td>{{state}}</td>
					<td>{{zip}}</td>
					<td>{{billing}}</td>
					<td>{{shipping}}</td>
					<td>
						<span style='float:right'>
						</span>
					</td>
				</tr>
			{{/addresses}}
		</table>
		{{pagination:links}}
		<?php echo form_close(); ?>

		<br /><p>
		<a class='btn blue' href='{{x:uri x='ADMIN'}}/customers/'>Back to Customers</a>

	</div>
</section>