
<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<div id="sortable">

	<div class="one_full" id="">
		<section class="title">
			<h4>Edit Product Type</h4>
		</section>
		<section class="item">
				<div class="content">
					<div>
						<?php
						if($has_active)
						{
							echo "You have active records";
						}
						?>
					</div>
					<fibeldset>
						<ul>
							<li class="<?php echo alternator('', 'even'); ?>">
								<label for="name">Name<span>*</span>
									<small></small>
								</label>
								<div class="input" ><?php echo form_input('name', set_value('name', $type->name)); ?></div>
							</li>
							<li class="">
								<label for="default">Is Default<span></span></label>
								<div class="input"><?php echo form_dropdown('default', array(0=>'No',1=>'Yes'),  $type->default ); ?> </div>
							</li>	
							<li class="<?php echo alternator('', 'even'); ?>">
								<br />
								<label for="name">Slug<span>*</span>
									<small></small>
								</label>
								<div class="input" ><?php echo $type->slug; ?> 
									<br />
										<a target='new' class="" href="{{x:uri}}/products/type/<?php echo $type->slug; ?>">View {{x:uri}}/products/type/<?php echo $type->slug; ?></a>
									<br />
								</div>

							</li>	
							<li class="<?php echo alternator('', 'even'); ?>">
								<br />
								<label for="name">Custom View File<span>*</span>
									<small></small>
								</label>
								<div class="input" >
									<code>nitrocart_product_type_<?php echo $type->slug; ?>.html</code>	
								</div>
								<br /><br />
							</li>	

							<li class="<?php echo alternator('', 'even'); ?>">
								<label for="name">Properties<span>*</span>
									<small></small>
								</label>
								<div>
									<div class="input">
										<?php echo form_multiselect('properties[]', $available_types, $set_types) ?>
										<?php if(count($set_types) < 3): ?>
											<br /><a class="" href="{{x:uri x='ADMIN'}}/attributes">Manage Attributes</a>
											<br />
											<p />
										<?php endif; ?>
									</div>
								</div>
							</li>														

						</ul>
				</fieldset>


				<div class="buttons">
					<button class="btn blue" value="save" name="btnAction" type="submit">Save</button>
					<a class="btn gray cancel" href="{{x:uri x='ADMIN'}}/products_types">Cancel</a>
				</div>	

			</div>
		</section>
	</div>


</div> <!--div id="sortable"-->


<?php echo form_close(); ?>