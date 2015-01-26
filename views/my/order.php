<div id="customer-portal">

	<h2>My Order</h2>


	{{ if order.paid_date == '' }}
		<a href="{{x:uri}}/payment/order/{{order.id}}">Pay now</a>
	{{ endif }}


	<h4>
		Order Details
	</h4>

	<!--  ORDER DETAILS  -->
	<table style="width: 100%">
		<tr>
			<td>
				<div>Order ID : {{order.id}}</div>
				<div>

				</div>
			</td>
			<td>
				<div>Order Status : {{order.status}}</div>
				<div>Payment Status : 
					{{orders:order_is_unpaid id="{{order.id}}" }}
						Not
					{{/orders:order_is_unpaid}} Paid.
				<div>Shipping Option : {{order.shipping_id}}</div>
			</td>
		</tr>
	</table>

	<hr />

	<!--  ORDER ADDRESS  -->
	<table style="width: 100%">
		<tr>
			<td>
				<b>Billing Address</b>
			</td>
			<td>
				<b>Shipping Address</b>
			</td>
		</tr>
		<tr>
				<td>

					{{invoice.first_name}} {{invoice.last_name}}<br />
					{{invoice.company}}<br />
					{{invoice.address1}} {{invoice.address2}}<br />
					{{invoice.city}} {{invoice.zip}}<br />
					{{if invoice.email}}
						<a href='mailto:{{invoice.email}}'>
							{{invoice.email}}
						</a><br />
					{{endif}}
				</td>
				<td>
					{{shipping.first_name}} {{shipping.last_name}}<br />
					{{shipping.company}}<br />
					{{shipping.address1}} {{shipping.address2}}<br />
					{{shipping.city}} {{shipping.zip}}<br />
					{{if shipping.email}}
						<a href='mailto:{{shipping.email}}'>
							{{shipping.email}}
						</a><br />
					{{endif}}
				</td>
		</tr>
	</table>

	<h4>Order Items</h4>


	<table style="width: 100%">
		<thead>
			<tr>
				<th>Image</th>
                <th>QTY</th>
				<th>Name</th>
				<th>Price</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>

			{{contents}}
				<tr>
					<td>
                        {{nitrocart_gallery:cover product_id='{{product_id}}' }}
                                <img src="{{ src }}" alt="{{ alt }}"  height="100" />
			             {{/nitrocart_gallery:cover}}  						
					</td>
					<td>{{qty}}</td>
					<td>{{title}}</td>
					<td><a href="{{x:uri}/products/product/{{ product_id }}">view</a></td>
				</tr>

			{{/contents}}

		</tbody>

	</table>

</div>