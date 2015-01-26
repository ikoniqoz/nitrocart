			<fieldset>
					<h3>
						Variances allow you to define an option that may incur a different price for your product.
						Users can still add items to the cart with out a variance. This sale will incur no charges.
					</h3>
			</fieldset>	

			<fieldset>
				<table class="prices_list">
					<tr>
						<th>ID</th>
						<th>RRP</th>
						<th>Base</th>
						<th>Price</th>
						<th>Name</th>
						<th>Is Active</th>
						<th>Can Discount</th>
					</tr>
					<?php foreach($prices AS $price_record) : ?>	
						<tr pr-id="<?php echo $price_record->id; ?>">
							<td><?php echo $price_record->id; ?></td>
							<td><?php echo nc_currency_symbol(). ' ' .$price_record->rrp; ?></td>
							<td><?php echo nc_currency_symbol(). ' ' .$price_record->base; ?></td>
							<td><?php echo nc_currency_symbol(). ' ' .$price_record->price; ?></td>
							<td><?php echo $price_record->name; ?></td>
							<td><?php echo yesNoBOOL($price_record->available); ?></td>
							<td><?php echo yesNoBOOL($price_record->discountable); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>					
			</fieldset>			