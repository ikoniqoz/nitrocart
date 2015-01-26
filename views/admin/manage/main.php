<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

<div id="sortable">

<div class="one_full" id="">

	<section class="title">
		<h4><?php echo lang('nitrocart:manage:title'); ?></h4>
		<a class="" title=""></a>
	</section>
	<section class="item">

		<div class="content">
			<ul class="set_menu">
				<?php foreach ($thesettings as $settings): ?>
					<?php  $this->load->library('settings/settings');?>
					<?php  $form = $this->settings->form_control($settings);?>
					<li class="set_menu">
						<label>
								<?php echo $settings->title;?><br/>
								<small><?php echo $settings->description;?></small>
						</label>
						<div>
							<?php echo $form;?>
						</div>
					</li>
				<?php endforeach;?>
			</ul>
			<div class="buttons">
				<button class="btn blue" value="save_exit" name="btnAction" type="submit">
					<span><?php echo lang('nitrocart:manage:save_exit');?></span>
				</button>
				<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
			</div>
		</div>
	</section>

</div>

</div>
<style>
ul.set_menu li.set_menu
{
	margin:15px;
	border-bottom: 1px solid #ccc;
	margin-bottom:25px;
	margin-top:25px;
	padding:20px;
}
</style>
<?php echo form_close(); ?>