<div>

	<form name='form1' method='POST' action='{{gdata.uri}}'>

		<input type='hidden' name='vendor_name' value='{{gdata.vendorname}}'>
		<input type='hidden' name='reply_link_url' value='{{x:uri}}/payment/rc/{{order.id}}'>
		<input type='hidden' name='return_link_url' value='{{x:uri}}/my/orders/order/{{order.id}}'>


		<!--Billing Information:-->
		<input type='hidden' name='Billing_name' value='{{billing.last_name}}, {{billing.first_name}}'>
		<input type='hidden' name='Billing_address_1' value='{{billing.address1}}'>
		<input type='hidden' name='Billing_address_2' value='{{billing.address2}}'>
		<input type='hidden' name='Billing_city' value='{{billing.city}}'>

		<!--Delivery Information:-->
		<input type='hidden' name='Delivery_name' value='{{shipping.last_name}}, {{shipping.first_name}}'>
		<input type='hidden' name='Delivery_address_1' value='{{shipping.address1}}'>
		<input type='hidden' name='Delivery_address_2' value='{{shipping.address2}}'>
		<input type='hidden' name='Delivery_postcode' value='{{shipping.zip}}'>
		<input type='hidden' name='Delivery_city' value='{{shipping.city}}'>

		<!--Other Contact Information:-->
		<input type='hidden' name='Contact_email' value='{{billing.email}}' >

		<input type='hidden' name='information_fields' value='Billing_name,Billing_address_1,Delivery_postcode,Delivery_city,Billing_address_2,Delivery_address_1,Delivery_address_2,Order_Total,Total_Tax_GST' >
		<input type='hidden' name='return_link_text' value='{{gdata.returnlinktext}}'>
		<input type='hidden' name='Total_Tax_GST' value='$ {{order.total_tax}}'>		


		<!--Product Data-->
	<?php $item_counter = 1;?>
	<?php foreach($order_items as $line_item): ?>
		<input type='hidden' name='(_<?php echo $item_counter;?>_)_<?php echo $line_item->name;?>_-_<?php echo nc_variant_name($line_item->variant_id);?>' value='<?php echo $line_item->qty;?>,<?php echo $line_item->price;?>'>
		<?php $item_counter++;?>
	<?php endforeach;?>
		<input type='hidden' name='(_<?php echo $item_counter;?>_)_Shipping' value='1,{{order.total_shipping}}'>


	

		<input type='hidden' name='Total_tax' value='$ {{order.total_tax}}'>
		<input type='hidden' name='Order_Total' value='$ {{order.total_totals}}'>
		<input type='hidden' name='suppress_field_names' value='true'>
		<input type='hidden' name='print_zero_qty' value='false'>


		<p>
			Your credit card payment will be processed securely by DirectOne Payment Solutions.
		</p>

		<p>
			Please click the DirectOne logo below to find out more about payment security.
		</p>

		<a href='http://www.directone.com.au/html/contacts/vendor_link.html' target='_blank'>
			<img src='http://www.directone.com.au/images/safe_link.gif'>
		</a>

		<br />
		<br />



		<h4>Payment Instructions</h4>

	    <p>
	    	Please pay  {{nitrocart:currency}} {{order.total_totals}} directly to:
	    </p>
	    <p>
	    	{{gateway.desc}}
	    </p>

		<br />
			<input type='submit' value='Confirm and Pay with DirectOne'>
		<br />


	 </form>

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
	 				<td>{{order.count_items}}</td>
	 			</tr>
	 			<tr>
	 				<td>Shipping</td>
	 				<td>{{nitrocart:currency}} {{order.total_shipping}}</td>
	 			</tr>
	 			<tr>
	 				<td>Tax</td>
	 				<td>{{nitrocart:currency}} {{order.total_tax}}</td>
	 			</tr>	 			
	 			<tr>
	 				<td>TOTAL</td>
	 				<td>{{nitrocart:currency}} {{order.total_totals}}</td>
	 			</tr>
	 			<tr>
	 				<td>Subtotals</td>
	 				<td>{{nitrocart:currency}} {{order.total_subtotal}}</td>
	 			</tr>	 			
	 		</table>


	 		<br />
	 		Your IP Address: {{order.ip_address}} <br />

	 		<br />

		    <a href="{{x:uri}}">back to shop</a>

</div>
