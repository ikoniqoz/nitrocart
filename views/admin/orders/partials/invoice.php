<style>

.invoice_table * {font-family:courier;}
.invoice_table thead * {text-decoration: underline;color:#333;}
.invoice_table tbody * {color:#333;}
.invoice_table tfoot * {font-weight:bold;color:#000;}
</style>

<fieldset>
	<table class='invoice_table'>
		<thead>
			<tr>
				<td>Title</td>
				<td>QTY</td>
				<td>Price</td>
				{{if base_amount_pricing === true }}
					<td>Base</td>
				{{ endif }}
				<td>Tax</td>				
				<td>Discount</td>
				<td>SubTotal</td>
				<td>Total (Tax Inc)</td>
			</tr>
		</thead>
		<tbody>
			{{invoice_items}}
				<tr>
					<td>
						{{title}}
					</td>
					<td>{{qty}}</td>
					<td>{{nitrocart:currency}} {{price}}</td>
					{{if base_amount_pricing === true }}
						<td>{{nitrocart:currency}} {{base}}</td>
					{{ endif }}					
					<td>{{nitrocart:currency}} {{tax}}</td>
					<td><span class='tooltip' title='{{discount_message}}'>{{nitrocart:currency}} {{discount}}</span></td>					
					<td>{{nitrocart:currency}} {{subtotal}}</td>
					<td>{{nitrocart:currency}} {{total}}</td>
				</tr>
			{{/invoice_items}}
			<!-- totals row-->
		</tbody>
		<tfoot style='font-weight:bold'>
				<tr>
					<td>TOTALS</td>
					<td></td>
					<td></td>
					{{if base_amount_pricing === true }}
						<td></td>
					{{ endif }}						
					<td><?php echo nc_format_price( $order->total_tax ); ?></td>
					<td><?php echo nc_format_price( $order->total_discount); ?></td>
					<td><?php echo nc_format_price( ($order->total_subtotal)); ?></td>
					<td><?php echo nc_format_price( $order->total_totals); ?></td>
				</tr>
		</tfoot>
	</table>
</fieldset>
