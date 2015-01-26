<section class="title">

	<h4><?php echo sprintf(lang('nitrocart:common:edit'), $type->name); ?></h4>

</section>

<?php echo form_open_multipart("nitrocart/admin/affiliates/edit/{$id}"); ?>

<?php if (isset($id) AND $id > 0): ?>

	<?php echo form_hidden('id', $id); ?>

<?php endif; ?>

<section class="item form_inputs">

	<div class="content">
		<fieldset>
			<ul>
				<li class="<?php echo alternator('even', ''); ?>">
					<label for="name"><?php echo lang('nitrocart:common:name');?><span>*</span></label>
					<div class="input">
						<?php echo form_input('name', set_value('name', $type->name), 'id="name" '); ?>
					</div>
				</li>
			</ul>
		</fieldset>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save_exit','save', 'cancel'))); ?>
		</div>

	</div>

</section>

<?php echo form_close(); ?>