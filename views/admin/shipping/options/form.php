<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
 <?php echo form_hidden('id', $id); ?>
<div id="sortable">

	<div class="one_half" id="">
	    <section class="title">
			<h4><?php echo lang('nitrocart:shipping:edit_title') ; ?></h4>
	    </section>
		<section class="item">
			<div class="content">
				<fieldset>
					<ul>
						<li class="">
							<label for="name"><?php echo lang('nitrocart:shipping:name') ; ?><span></span></label>
							<div class="input"><?php echo $name; ?></div>
						</li>
						<li class="">
							<label for="name"><?php echo lang('nitrocart:shipping:field_title') ; ?> <span>*</span></label>
							<div class="input"><?php echo form_input('title', set_value('name', $title), 'class="width-15"'); ?></div>
						</li>
						<li class="">
							<label for="name"><?php echo lang('nitrocart:shipping:enable') ; ?> <span></span></label>
							<div class="input"><?php echo form_dropdown('enabled', array('0'=>'Disabled','1'=>'Enabled'), set_value('enabled', $enabled), 'class="width-15"'); ?></div>
						</li>
						<li class="<?php echo alternator('', 'even'); ?>">
							<label for="description"><?php echo lang('nitrocart:shipping:description') ; ?><span>*</span></label>
							<div class="input"><?php echo form_input('description', set_value('description', $description)); ?></div>
						</li>							
					</ul>
				</fieldset>
			</div>

			<div class="content">
				<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save_exit','save', 'cancel'))); ?>
			</div>

	    </section>

	</div>


	<div class="one_half last" id="">
	    <section class="title">
	    	<h4>Custom Options</h4>
	    </section>
		<section class="item">
			<div class="content">
					<fieldset>
						<?php $this->load->file($form); ?>
					</fieldset>
			</div>
	    </section>

	</div>


</div>	

<?php echo form_close(); ?>