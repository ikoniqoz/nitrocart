<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<div id="sortable">

	<div class="one_half" id="">
		<section class="title">
			<h4>Create A Product Type</h4>
		</section>
		<section class="item">
				<div class="content">
					<fieldset>
						<ul>
							<li class="<?php echo alternator('', 'even'); ?>">
								<label for="name">Name<span>*</span>
									<small></small>
								</label>
								<div class="input" ><?php echo form_input('name', set_value('name', $name)); ?></div>
							</li>
							<li class="">
								<label for="default">Is Default<span></span></label>
								<div class="input"><?php echo form_dropdown('default', array(0=>'No',1=>'Yes'),  $default ); ?> </div>
							</li>	
						</ul>
				</fieldset>


				<div class="buttons">
					<button class="btn blue" value="save" name="btnAction" type="submit">Create</button>
					<a class="btn gray cancel" href="{{x:uri x='ADMIN'}}/products_types">Cancel</a>
				</div>	

			</div>
		</section>
	</div>


</div> <!--div id="sortable"-->


<?php echo form_close(); ?>
