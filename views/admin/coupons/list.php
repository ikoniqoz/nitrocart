
<section class="title">
	<h4>Coupons</h4>
</section>

<section class="item">

	<div class="content">
	
		<table>
				<tr>
					<th>ID</th>
					<th>Product ID</th>
					<th>Coupon Code</th>
					<th>Max Use</th>
					<th>Percentage</th>
					<th>Usage Count</th>
					<th>Active</th>
					<th class='actions'><?php echo lang('nitrocart:admin:actions');?></th>
				</tr>
			
			{{coupons}}
				<tr>
					<td>{{id}}</td>
					<td>{{product_id}}</td>
					<td>{{code}}</td>
					<td>{{max_use}}</td>
					<td>{{pcent}}</td>
					<td>{{used_count}}</td>
					<td>{{x:bool value=enabled }}</td>
					<td>
						<span style='float:right'>
							<a class='button edit' href='{{x:uri x='ADMIN'}}/product/edit/{{product_id}}'>Product</a>
							<a class='button edit' href='{{x:uri x='ADMIN'}}/coupons/edit/{{id}}'>Edit</a>
							<a class='button delete confirm' href='{{x:uri x='ADMIN'}}/coupons/delete/{{id}}'>Delete</a>
						</span>
					</td>
				</tr>
			{{/coupons}}
		</table>

		{{pagination:links}}
	
	</div>

</section>