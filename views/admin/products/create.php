<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<div id="sortable">

	<div class="one_full" id="">
		<section class="title">
			<h4>Product</h4>
		</section>
		<section class="item">
				<div class="content">
					<fieldset>
						<p>
							Define your product. You can not change the product type once the product is created.
						</p>
					


						<table>
							<tr>
								<td>
									<?php echo lang('nitrocart:products:name'); ?> <span>*</span>
								</td>
								<td><?php echo form_input('name', set_value('name', $name), "placeholder='Name of Product'"); ?></td>
							</tr>
							<tr>
								<td><?php echo lang('nitrocart:products:default_price'); ?><span></span>
									<small></small>
								</td>
								<td class="input">
									<div class="input"><?php echo form_input('price', null,"placeholder='0.00'"); ?></div>
								</td>
							</tr>
							<tr>
								<td for="">Product Type<span></span><br /></td>
								<td class="input">
									<?php 
										switch( count($available_types) )
										{
											case 0:
													echo "Error: Please create a produt type.";
												break;
											case 1:
													//get first key
													reset($available_types);
													$first_key = key($available_types);
												echo $available_types[$first_key]."  [<a href='{{x:uri x='ADMIN'}}/products_types/edit/{$first_key}'>edit</a>] [<a href='{{x:uri x='ADMIN'}}/products_types/create/'>add new</a>]<input type='hidden' name='type_id' value='{$first_key}'>";
												break;
											default:
												echo form_dropdown('type_id', $available_types,$default_typeID);
												break;																								
										}

									?>
								</td>
							</tr>							
							<?php

								$display_taxes = (count($available_taxes)==0)?false:true;
							?>

							<tr>
								<td>Tax Class</td>
								<td class="input">
									<?php if($display_taxes):?>
										<?php echo form_dropdown('tax_id', $available_taxes,$default_taxID ); ?>
									<?php else:?>
										<!-- null is standard-->
										No Tax
										<input type='hidden' name='tax_id' value=''>
									<?php endif;?>
								</td>
							</tr>							

							<?php
								//var_dump($available_groups);
								$display_groups = (count($available_groups)==1)?false:true;

								// We need the first key in the array if there is only 1 option.
								// This is so we dont have to display t he Dropdown select
								reset($available_groups);
								$first_key = key($available_groups);
								$first_text = $available_groups[$first_key];
							?>
							<tr>
								<td>
									Package Group <span>*</span>
								</td>
								<td>
									
									<?php if($display_groups):?>
										<?php echo form_dropdown('pkg_group_id', $available_groups , $default_groupID ); ?>
									<?php else:?>
											<!-- null is standard-->
											<?php echo $first_text;?>
											<?php $et = "  [<a href='{{x:uri x='ADMIN'}}/packages_groups/edit/{$first_key}'>edit</a>] [<a href='{{x:uri x='ADMIN'}}/packages_groups/create/'>add new</a>]<input type='hidden' name='pkg_group_id' value='{$first_key}'>"; ?>
											<input type='hidden' name='pkg_group_id' value='<?php echo $first_key;?>'> <?php echo $et;?>
									<?php endif;?>
								</td>
							</tr>

							<tr>
								<td>
									Shipping Zone<span>*</span>
								</td>
								<td>
									<?php echo form_dropdown('zone_id', $available_zones , $default_zone_ID ); ?>
								</td>
							</tr>

						</table>
					
				</fieldset>

				<div class="buttons">
					<button class="btn blue" value="save" name="btnAction" type="submit">Create &amp; Edit</button>
					<a class="btn gray cancel" href="{{x:uri x='ADMIN'}}/products">Cancel</a>
				</div>				
			</div>
		</section>
	</div> <!--div class="one_half" id=""-->

</div> <!--div id="sortable"-->

<?php echo form_close(); ?>