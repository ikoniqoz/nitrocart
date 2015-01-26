	<div class="content VarianceContext">
				<input type='hidden' name='editvariance_id' value='<?php echo $variance->id;?>'>

			<fieldset>

						<div class="input">

							<h4>Edit Variant Name</h4>

							<table>

								<tr>
									<td>
										<label for=""><?php echo lang('nitrocart:products:variation_name');?></span><br /></label>
										<?php echo form_input('var_name',(isset($variance))?$variance->name:'Standard'); ?>
									</td>
								</tr>

							
							</table>

						</div>

						<div class="input">
							<a class="editVariantName btn orange" style='cursor:pointer'>Save</a>
						</div>

						<div class="input">

						</div>

				</fieldset>

</div>