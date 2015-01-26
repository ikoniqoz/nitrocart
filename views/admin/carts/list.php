
<section class="title">
	<h4>Active Shopping carts</h4>
</section>

<section class="item">

	<div class="content">
	
		<table>
				<tr>
					<th>USER-ID</th>
					<th>User name</th>
					<th>Items QTY</th>
					<th>Total Value</th>
					<th>Last Seen</th>
					<th>First Seen</th>
					<th class='actions'><?php echo lang('nitrocart:admin:actions');?></th>
				</tr>
			
			{{carts}}
				<tr>
					<td>{{user_id}}</td>
					<td>{{username}}</td>
					<td>{{qty}}</td>
					<td>{{value}}</td>
					<td>{{date}}</td>
					<td>{{oldest}}</td>
					<td>
						<span style='float:right'>
							<a class='button modal' href='{{x:uri x='ADMIN'}}/carts/view/{{user_id}}'>Items</a>
							<a class='button red delete confirm' href='{{x:uri x='ADMIN'}}/carts/delete/{{user_id}}'>Delete</a>
						</span>
					</td>
				</tr>
			{{/carts}}
		</table>

		{{pagination:links}}
	
	</div>

</section>