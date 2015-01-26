<div>

 		<h4>Payment Instructions</h4>

	    <p>
	    	Please pay  {{nitrocart:currency}} {{order.total_amount_order_wt}} directly to:
	    </p>
	    <p>
	    	{{gateway.description}}
	    </p>

 		<br />
 		<br />

 		<h4>Order Details</h4>

 		Order ID: {{order.id}} <br />

		{{if order.pin != ''}}
			Your PIN number is {{order.pin}}<br />
		{{endif}}

 		Order Status: {{order.status}} <br />

 		Billing Email: {{billing.email}} <br />

 		<table>
 			<tr>
 				<td>Total Items</td>
 				<td>{{order.total_count_items}}</td>
 			</tr>
 			<tr>
 				<td>Tax</td>
 				<td>{{nitrocart:currency}} {{order.total_tax}}</td>
 			</tr>
 			<tr>
 				<td>Shipping</td>
 				<td>{{nitrocart:currency}} {{order.total_shipping}}</td>
 			</tr>
 			<tr>
 				<td>TOTAL</td>
 				<td>{{nitrocart:currency}} {{order.total_totals}}</td>
 			</tr>
 		</table>


 		<br />
 		Your IP Address: {{order.ip_address}} <br />


 		<br />


	    <a href="{{x:uri}}">back to shop</a>

</div>
