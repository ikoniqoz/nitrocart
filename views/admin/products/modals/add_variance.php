	<div class="content VarianceContext">
				<fieldset>
						<div class="input">
							<h4><?php echo lang('nitrocart:products:add_variance');?></h4>
							<table>
								<tr>
									<td>
										<label for=""><?php echo lang('nitrocart:products:variation_name');?><br /></label>
										<?php echo form_input('var_name',(isset($variance))?$variance->name:'Standard'); ?>
									</td>
									<td>
										<label for="">SKU Code<br /></label>
										<?php echo form_input('var_sku',(isset($variance))?$variance->sku:''); ?>

										<div style='display:none'>
											<label for=""><?php echo lang('nitrocart:products:active');?> <span></span><br /></label>
											<?php echo form_dropdown('available',array(0=>'Disabled',1=>'Enabled'),(isset($variance))?$variance->available:1); ?>
										</div>
									</td>									
								</tr>
							</table>

						</div>

						<div class="input">
							<a class="addVarianceRecord btn orange" style='cursor:pointer'><?php echo lang('nitrocart:products:add');?></a>
						</div>

						<div class="input">
						</div>

				</fieldset>
		</div>