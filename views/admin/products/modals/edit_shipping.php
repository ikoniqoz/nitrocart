	<div class="content VarianceContext">
				<input type='hidden' name='editvariance_id' value='<?php echo $variance->id;?>'>

			<fieldset>

						<div class="input">

							<h4>Shipping</h4>

							<table>
								<tr>
									<td>
										<label for="">Package Class/Group <span>*</span><br /></label>
										<?php echo form_dropdown('pkg_group_id', $available_groups,set_value('pkg_group_id',(isset($variance))?$variance->pkg_group_id:NULL) ); ?>
									</td>
									<td>
										<label for="">Shipping Zone <span></span><br /></label>
										<?php echo form_dropdown('zone_id', array(0=>'Default') + $shipping_zones ,set_value('zone_id',(isset($variance))?$variance->zone_id:$default_id) ); ?>
									</td>
								</tr>
								<tr>
									<td>
										<label for="rrp">Height <span></span><br /></label>
										<?php echo form_input('height', (isset($variance))?$variance->height:0.0, "placeholder='0.0'"); ?> cm
									</td>
									<td>
										<label for="rrp">Weight <span></span><br /></label>
										<?php echo form_input('weight', (isset($variance))?$variance->weight:0.0, "placeholder='0.0'"); ?> Kg
									</td>
								</tr>
								<tr>
									<td>
										<label for="rrp">Width <span></span><br /></label>
										<?php echo form_input('width', (isset($variance))?$variance->width:0.0, "placeholder='0.0'"); ?> cm
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td>
										<label for="rrp">Length <span></span><br /></label>
										<?php echo form_input('length', (isset($variance))?$variance->length:0.0, "placeholder='0.0'"); ?> cm
									</td>
									<td>
									</td>
								</tr>
							</table>

						</div>


						<div class="input">
							<a class="editShippingData btn orange"  style='cursor:pointer'>Save/Confirm</a>
						</div>

						<div class="input">

						</div>

				</fieldset>

</div>