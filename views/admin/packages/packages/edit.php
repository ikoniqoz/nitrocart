<div id="sortable">

	<?php if(isset($id)): ?>
		<?php echo form_open_multipart(NC_ADMIN_ROUTE.'/packages/edit/'.$id); ?>
	<?php else: ?>
		<?php echo form_open_multipart(NC_ADMIN_ROUTE.'/packages/create/'); ?>
	<?php endif; ?>

	<div class="one_half" id="">

		<section class="title">

			<?php if(isset($id)): ?>
				<h4><?php echo lang('nitrocart:packages:edit');?></h4>
			<?php else: ?>
				<h4><?php echo lang('nitrocart:packages:new');?></h4>
			<?php endif; ?>
		</section>

		<input type='hidden' name='is_digital' value='0'>
		<input type='hidden' name='is_shipping' value='1'>

		<section class="item form_inputs">

			<div class="content">
				<fieldset>

					<h3>Identification</h3>

							<label for="name"><?php echo lang('nitrocart:packages:name');?><span>*</span></label>
							<div class="input">
								<?php echo form_input('name', set_value('name', $name), 'id="name"  placeholder="Enter package name here"'); ?>
							</div>


							<label for="name">Code<span></span></label>
							<div class="input">
								<?php echo form_input('code', set_value('code', $code), 'id="code"  placeholder="Enter package code here"'); ?>
							</div>


							<label for="name">Package Class/Group<span>*</span>
							<i>You must assign a package to a group. i.e Envelopes,Letters,Parcels etc..</i>
							</label>
							<div class="input">
								<?php echo form_dropdown('pkg_group_id', $available_groups,set_value('pkg_group_id',(isset($pkg_group_id))?$pkg_group_id:NULL) ); ?>
							</div>

					<h3>Dimentions</h3>

					<h4>Inner Dimentions</h4>

							<label for="name">Inner Height<span>*</span></label>
							<div class="input">
								<?php echo form_input('height', set_value('height', $height), 'id="height"  placeholder="0"'); ?> cm.
							</div>

							<label for="name">Inner Width<span>*</span></label>
							<div class="input">
								<?php echo form_input('width', set_value('width', $width), 'id="width"  placeholder="0"'); ?> cm.
							</div>


							<label for="name">Inner Length<span>*</span></label>
							<div class="input">
								<?php echo form_input('length', set_value('length', $length), 'id="length"  placeholder="0"'); ?> cm.
							</div>

								<span>
									<a href='#' class='btn small green boxduplicate'>Outer is Same as Inner</a>
								</span>

					<h4>Outer Dimentions</h4>

							<label for="name">Outer Height<span>*</span></label>
							<div class="input">
								<?php echo form_input('outer_height', set_value('outer_height', $outer_height), 'id="outer_height"  placeholder="0"'); ?> cm.


							</div>

							<label for="name">Outer Width<span>*</span></label>
							<div class="input">
								<?php echo form_input('outer_width', set_value('outer_width', $outer_width), 'id="outer_width"  placeholder="0"'); ?> cm.
							</div>

							<label for="name">Outer Length<span>*</span></label>
							<div class="input">
								<?php echo form_input('outer_length', set_value('outer_length', $outer_length), 'id="outer_length"  placeholder="0"'); ?> cm.
							</div>

						<h3>Weight</h3>

							<label for="name">Max Weight<span>*</span></label>
							<div class="input">
								<?php echo form_input('max_weight', set_value('max_weight', $max_weight), 'id="max_weight"  placeholder="0"'); ?> Kg.
							</div>

							<label for="name">Package Weight (Empty weight)<span>*</span></label>
							<div class="input">
								<?php echo form_input('cur_weight', set_value('cur_weight', $cur_weight), 'id="cur_weight"  placeholder="0.200"'); ?> Kg.
							</div>


				</fieldset>

				<div class="buttons">
					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
				</div>

			</div>

		</section>
	</div>

	<?php echo form_close(); ?>
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
										<?php echo $created; ?>
									</div>
								</li>
								<li class="">
									<label for="name">
										Date Updated:
									</label>
									<div class="input">
										<?php echo $updated; ?>
									</div>
								</li>
							</ul>
						</fieldset>
					</div>
				</section>
			</div>
		<?php endif; ?>
