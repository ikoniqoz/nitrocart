<div id="sortable">

	<div class="one_half" id="">

		<section class="title">

			<?php if(isset($id)): ?>
				<h4><?php echo lang('nitrocart:packages:new');?></h4>
			<?php else: ?>
				<h4><?php echo lang('nitrocart:packages:new');?></h4>
			<?php endif; ?>
		</section>

		<?php if(isset($id)): ?>
			<?php echo form_open_multipart(NC_ADMIN_ROUTE.'/packages_groups/edit/'.$id); ?>
		<?php else: ?>
			<?php echo form_open_multipart(NC_ADMIN_ROUTE.'/packages_groups/create/'); ?>
		<?php endif; ?>

		<input type='hidden' name='is_digital' value='0'>
		<input type='hidden' name='is_shipping' value='1'>

		<section class="item form_inputs">

			<div class="content">
				<fieldset>
					<ul>
						<li class="">
							<label for="name"><?php echo lang('nitrocart:packages:name');?><span>*</span></label>
							<div class="input">
								<?php echo form_input('name', set_value('name', $name), 'id="name"  placeholder="Enter package name here"'); ?>
							</div>
						</li>
						<li class="">
							<label for="default">Is Default<span></span></label>
							<div class="input"><?php echo form_dropdown('default', array(0=>'No',1=>'Yes'),  $default ); ?> </div>
						</li>	
					</ul>


				</fieldset>

				<div class="buttons">
					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))); ?>
					<a href="{{x:uri x='ADMIN'}}/packages_groups/" class='btn gray'>Cancel</a>
				</div>

			</div>

		</section>
		<?php echo form_close(); ?>

	</div>



		<?php if(isset($id)): ?>
			<div class="one_half last" id="">

				<section class="title" >
					<h4>Meta Data</h4>
				</section>
				<section class="item form_inputs">

					<div class="content">
						<fieldset>
							<ul>
								<li class="">
									<label for="name">
										Created By:
									</label>
									<div class="input">
										<?php echo user_displayname($created_by, true); ?>
									</div>
								</li>
								<li class="">
									<label for="name">
										Date Created:
									</label>
									<div class="input">
										<?php echo nc_format_date($created,''); ?>
									</div>
								</li>
								<li class="">
									<label for="name">
										Date Updated:
									</label>
									<div class="input">
										<?php echo nc_format_date($updated,''); ?>
									</div>
								</li>
							</ul>
						</fieldset>
					</div>
				</section>
			</div>
		<?php endif; ?>



</div>