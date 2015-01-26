			<fieldset>
					<h3>
						Manage your product variations.
                        <small>
                            A variation  may affect the cost, shipping or handling of the item.
                        </small>
					</h3>

					<a class="sbtn glow modal" href="{{x:uri x='ADMIN'}}/variances/create/<?php echo $id;?>">Add variance</a>
			</fieldset>

			<fieldset style='float:left;width:70%'>
				<table class="prices_list">
					<tr>
						<th>ID</th>
						<th>SKU</th>
						<th>On Hand</th>
						{{if base_amount_pricing == true }}
							 <th>Base</th>
						{{endif}}
						<th>Price</th>
						<th class='tooltip-s' title='Click to change'>Active</th>
						<th class='tooltip-s' title='Click to change'>Discountable</th>
						<th class='tooltip-s' title='Click to change'>Shippable</th>
						<th class='actions'>Actions</th>
					</tr>
					<?php foreach($prices AS $price_record) : ?>
						<tr pr-id="<?php echo $price_record->id; ?>">
							<td>
								<a var-id="<?php echo $price_record->id; ?>" class='button view_variance_button'  href="{{x:uri x='ADMIN'}}/product/variant/<?php echo $price_record->id;?>"><?php echo $price_record->id; ?></a>
							</td>
							<td>
								<?php $price_record->sku = (trim($price_record->sku)=='')? 'SET A CODE' :$price_record->sku ; ?>
								<?php $price_record->sk_on_hand =  'N/A'; ?>
								<?php echo "<a href='{{x:uri x='ADMIN'}}/variances/edit/{$price_record->id}' class='modal'>{$price_record->sku}</a>";?>
							</td>		
							<td><?php echo "<a>{$price_record->sk_on_hand}</a></td>";?>
							{{if base_amount_pricing == true }}
								<td><?php echo "<a href='{{x:uri x='ADMIN'}}/variances/price/get/{$price_record->id}' class='modal'>".nc_currency_symbol()." {$price_record->base}</a></td>";?>			
							{{endif}}

							<td><?php echo "<a href='{{x:uri x='ADMIN'}}/variances/price/get/{$price_record->id}' class='modal'>".nc_currency_symbol()." {$price_record->price}</a></td>";?>
							
							<td>
								<a class='call_toggle_pr ' func='edit_available' href="{{x:uri x='ADMIN'}}/variances/toggle_value/<?php echo $price_record->id;?>"><?php echo yesNoBOOL($price_record->available); ?></a>
							</td>
							<td>
								<a class='call_toggle_pr ' func='edit_discountable' href="{{x:uri x='ADMIN'}}/variances/toggle_value/<?php echo $price_record->id;?>"><?php echo yesNoBOOL($price_record->discountable); ?></a>
							</td>	
							<td>
								<a class='call_toggle_pr ' func='toggle_shippable' href="{{x:uri x='ADMIN'}}/variances/toggle_value/<?php echo $price_record->id;?>"><?php echo yesNoBOOL($price_record->is_shippable); ?></a>
							</td>														
							<td>
								<span style='float:right'>
									<a href="{{x:uri x='ADMIN'}}/variances/duplicate_aj/<?php echo $price_record->id;?>" class='copyVariantAJ button green tooltip-s' title='Copy'><i class='icon-copy'></i></a>	
									<a href="{{x:uri x='ADMIN'}}/attributes/ajax_get/<?php echo $id;?>/<?php echo $price_record->id;?>" class='button blue modal tooltip-s' title='Attributes'><i class='icon-star'></i></a>								
									<a pr-id='<?php echo $price_record->id;?>' href="#" class='delPriceRecord button red delete_button tooltip-s' title='Delete'>&times;</i></a>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</fieldset>

			<fieldset style='width:30%;float:right'>
				<table class="">
					<tr><td class='vcal' id='VarianceContentArea_List'></td></tr>
				</table>
			</fieldset>