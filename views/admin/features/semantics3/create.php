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