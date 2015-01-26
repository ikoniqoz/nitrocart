
<section class="title">
	<h4>Shopping cart : User : {{username}}</h4>
</section>

<section class="item">

	<div class="content">
	
		<table>
				<tr>
					<th>Row ID</th>
					<th>Product ID</th>
					<th>Variance ID</th>
					<th>Product Name</th>
					<th>Date</th>
					<th>QTY</th>
					<th>Price</th>
					<th class='actions'></th>
				</tr>
			
			{{items}}
				<tr>
					<td>{{id}}</td>
					<td>{{product_id}}</td>
					<td>{{variance_id}}</td>
					<td>{{name}}</td>
					<td>{{date}}</td>
					<td>{{qty}}</td>
					<td>{{price}}</td>
					<td>
						<span style='float:right'>
						</span>
					</td>
				</tr>
			{{/items}}
		</table>

		{{pagination:links}}
	
	</div>

</section>