<div id="MerchantPage">

	<form id="payment" action="{{x:uri}}/payment/process/{{order.id}}" method="POST">

			<table>

				<tr>
					<td>Name on Card: </td>
					<td><?php echo form_input('name'); ?></td>
				</tr>
				<tr>
					<td>Card Type: </td>
					<td><?php echo form_dropdown('card_type', $default_cards); ?></td>
				</tr>
				<tr>
					<td>Card No: </td>
					<td><?php echo form_input('card_no'); ?></td>
				</tr>		
				<tr>
					<td>Start month: </td>
					<td><?php echo form_dropdown('start_month', $months); ?></td>
				</tr>	

				<tr>
					<td>Start date: </td>
					<td> <?php echo form_dropdown('start_year', $start_years); ?></td>
				</tr>	

				<tr>
					<td>Expiry date:</td>
					<td><?php echo form_dropdown('exp_month', $months); ?></td>
				</tr>			

				<tr>
					<td>Expiry date:</td>
					<td><?php echo form_dropdown('exp_year', $years); ?></td>
				</tr>	

				<tr>
					<td>Issue Number:</td>
					<td><?php echo form_input('card_issue'); ?></td>
				</tr>		

				<tr>
					<td>CSC:</td>
					<td><?php echo form_input('csc'); ?></td>
				</tr>					
			</table>
	 

	    <button type="submit" class="contact-button"><span>Process</span></button>

	</form>

</div>