<section class="title">
	<h4><?php echo lang('nitrocart:gateways:title'); ?> : <?php echo $gateway->title;?></h4>
</section>
<section class="item">
	<div class="content">
		<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
		<?php echo form_hidden('id', $gateway->id); ?>
		<div class="form_inputs">
			<fieldset>
				<legend><?php echo lang('nitrocart:gateways:form_title'); ?></legend>
				<ul>
					<li class="<?php echo alternator('', 'even'); ?>">
						<label for="name"><?php echo lang('nitrocart:admin:name'); ?><span>*</span></label>
						<div class="input"><?php echo form_input('title', set_value('name', $gateway->title), 'class="width-15"'); ?></div>
					</li>
					<li class="<?php echo alternator('', 'even'); ?>">
						<label for="description"><?php echo lang('nitrocart:gateways:description'); ?><span>*</span></label>
						<div class="input"><?php echo form_textarea('description', set_value('description', $gateway->description), 'class="width-15"'); ?></div>
					</li>
				</ul>
			</fieldset>
			<fieldset>
				<legend><?php echo lang('nitrocart:gateways:options'); ?></legend>
				<?php $this->load->view('admin/merchant/'.$gateway->slug.'/form'); ?>
			</fieldset>
		</div>
		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
		</div>
		<?php echo form_close(); ?>
	</div>
</section>