<section class="title">
	<h4><?php echo lang('shop_variances:admin:new');?></h4>
</section>

<?php echo form_open_multipart('nitrocart/admin/affiliates/create/'); ?>

<section class="item form_inputs">

	<div class="content">
		<fieldset>
			<ul>
				<li class="<?php echo alternator('even', ''); ?>">
					<label for="name"><?php echo lang('nitrocart:admin:name');?><span>*</span></label>
					<div class="input">
						<?php echo form_input('name', set_value('name', ''), 'id="name"  placeholder="'.lang('nitrocart:admin:affiliates_placeholder').'"'); ?>
					</div>
				</li>
			</ul>
		</fieldset>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
		</div>

	</div>

</section>
<?php echo form_close(); ?>