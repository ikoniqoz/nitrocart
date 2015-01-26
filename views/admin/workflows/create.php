<div class="one_half" id="">

	<section class="title">
		<h4>Create new Workflow</h4>
	</section>

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>


	<section class="item form_inputs">

		<div class="content">

			<fieldset>
				<ul>
					<li>
						<a class='btn green' href="{{x:uri x='ADMIN'}}/workflows/">Back to List</a>
					</li>
					<li class="">
						<label for="name">Name<span>*</span></label>
						<div class="input">
							<?php echo form_input('name', set_value('name', $name), 'id="name" '); ?>
						</div>
					</li>

				</ul>
			</fieldset>

			<div class="buttons">
					<button class="btn blue" value="save_exit" name="btnAction" type="submit">
						<span>Save</span>
					</button>

					<a href="{{x:uri x='ADMIN'}}/workflows/" class="btn gray">Cancel</a>
			</div>

		</div>
	</section>
	<?php echo form_close(); ?>

</div>