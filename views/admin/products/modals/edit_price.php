	<div class="content VarianceContext">
				<input type='hidden' name='editvariance_id' value='<?php echo $variance->id;?>'>

			<fieldset>

						<div class="input">

							<h4>Pricing</h4>

							<table>

								<tr>
									<td>
										<label for="">Discountable <span></span><br /></label>
										<?php echo form_dropdown('discountable',array(0=>'Not Allowed',1=>'Allowed'),set_value('discountable', (isset($variance))?$variance->discountable:NULL)); ?>
									</td>
								</tr>

								<tr>									
									<td>
										<label for="">Price <?php echo nc_currency_symbol();?> <span>required</span><br /></label>
										<?php echo form_input('price', (isset($variance))?$variance->price:NULL, "placeholder='0.00'"); ?>
									</td>
								</tr>
								<tr>
									<td>
										<label for="rrp">RRP (Recomended retail Price) <?php echo nc_currency_symbol();?> <span></span><br /></label>
										<?php echo form_input('rrp', (isset($variance))?$variance->rrp:NULL, "placeholder='0.00'"); ?>
									</td>
								</tr>

								<tr>									
									<td>
										{{if base_amount_pricing }}										
											<label for="">Base Amount <?php echo nc_currency_symbol();?> <span>Single fee</span><br /></label>
											<?php echo form_input('base', (isset($variance))?$variance->base:NULL, "placeholder='0.00'"); ?>
										{{else}}
											<input type='hidden' name='base' value='0'>
										{{endif}}
									</td>
								</tr>
							</table>

						</div>

						<div class="input">
							<a class="editPriceData btn orange" style='cursor:pointer'>Save/Confirm</a>
						</div>

						<div class="input">

						</div>

				</fieldset>

</div>