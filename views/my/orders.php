<div id="">

	<h2>My Orders</h2>


	<div class="my-orders">

			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Date</th>

						<th>Total</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>

					{{items}}
					<tr>
						<td># {{id}}</td>
						<td>{{helper:date format="d-M-Y" timestamp=order_date}}</td>

						<td>{{nitrocart:currency}} {{total_amount_order_wt}}</td>
						<td>{{status}}</td>
						<td>
								<a href="{{x:uri}}/my/orders/order/{{id}}" class="">view</a>

								{{orders:order_is_unpaid id="{{id}}" }}
									<a href="{{x:uri}}/payment/order/{{id}}" class="">pay now</a>
								{{/orders:order_is_unpaid}}

								{{orders:order_is_paid id="{{id}}" }}
									Thank you for your payment
								{{/orders:order_is_paid}}
						 </td>

					</tr>
					{{/items}}

				</tbody>

			</table>

	</div>
</div>