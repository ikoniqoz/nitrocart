<div class="one_half" id="">
<section class="title">
	<?php if (isset($id) AND $id > 0): ?>
		<h4><?php echo sprintf('Edit', $name); ?></h4>
	<?php else: ?>
		<h4>New</h4>
	<?php endif; ?>
</section>
<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<?php if (isset($id) AND $id > 0): ?>
	<?php echo form_hidden('id', $id); ?>
	<input type="hidden" name="cid" id="cid" value="<?php echo $id; ?>" >
<?php endif; ?>
<section class="item form_inputs">
	<div class="content">
		<fieldset>
			<ul>
				<li class="">
					<label for="name"><?php echo lang('nitrocart:admin:name');?><span>*</span></label>
					<div class="input">
						<?php echo form_input('name', set_value('name', $name), 'id="name" '); ?>
					</div>
				</li>
				<li class="">
					<label for="name"><?php echo lang('nitrocart:workflows:progress');?><span>*</span></label>
					<div class="input">
						<?php echo form_input('pcent', set_value('pcent', $pcent), 'id="name" '); ?>
					</div>
				</li>	
				<li class="">
					<label for="name">Default<span>*</span></label>
					<div class="input">
						<?php echo form_dropdown('is_placed', [0=>'No', 1=>'Yes'] , $is_placed, 'id="is_placed" '); ?>
					</div>
				</li>								
			</ul>
		</fieldset>

		<div class="buttons">
				<button class="btn blue" value="save_exit" name="btnAction" type="submit">
					<span><?php echo lang('nitrocart:workflows:save_exit');?></span>
				</button>

				<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))); ?>
				<a href="{{x:uri x='ADMIN'}}/workflows/" class="btn gray"><?php echo lang('nitrocart:workflows:cancel');?></a>
		</div>

	</div>
</section>
<?php echo form_close(); ?>
</div>


<div class="one_half last" id="">

<section class="title" style="">
	<h4><?php echo lang('nitrocart:admin:actions');?></h4>
	<h4 style="float:right"></h4>
</section>
<section class="item form_inputs">
	<div class="content">
		<table class='sortable' id='sortable_list'>
			<thead>
				<tr>
					<th></th>
					<th style="width:30%"></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</section>
</div>
