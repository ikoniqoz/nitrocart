<section class="title">
	<h4><?php echo lang('nitrocart:admin:affiliates');?></h4>
	<h4 style="float:right">
		<a href="admin/nitrocart/affiliates/create" title="" class='tooltip-s img_icon_title img_create'>New</a>
	</h4>
</section>
<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<section class="item">
	<div class="content">
	<?php if (empty($affiliates)): ?>
		<div class="no_data">
		<p><?php echo lang('nitrocart:admin:no_data');?></p>
		<?php echo lang('no_items'); ?></div>
		</div>
	</section>
<?php else: ?>
	<table>
		<thead>
			<tr>
				<th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
				<th><?php echo lang('nitrocart:admin:id');?></th>
				<th><?php echo lang('nitrocart:admin:affiliates');?></th>
				<th style="width: 120px"></th>
			</tr>
		</thead>
		<tbody>

			<?php
				$data = new StdClass();
				foreach ($affiliates AS $type): ?>


							<tr class="<?php echo alternator('even', ''); ?>">

								<td><input type="checkbox" name="action_to[]" value="<?php echo $type->id; ?>"  /></td>
								<td><?php echo $type->id; ?></td>
								<td><?php echo $type->name; ?></td>
								<td>
									<span style="float:right;">

										<span class="sbtn-dropdown" data-buttons="dropdown">

												<a href="#" class="sbtn sbtn-rounded sbtn-flat-blue">
													<?php echo lang('nitrocart:common:actions');?>
													<i class="icon-caret-down"></i>
												</a>

												<!-- Dropdown Below Button -->
												<ul class="sbtn-dropdown">
													<li class=''>
														<a class="" href="<?php echo site_url('admin/nitrocart/affiliates/view/' . $type->id); ?>">
															<?php echo lang('nitrocart:admin:view');?>
														</a>
													</li>
													<li class=''>
														<a class="" href="<?php echo site_url('admin/nitrocart/affiliates/edit/' . $type->id); ?>">
															<?php echo lang('nitrocart:admin:edit');?>
														</a>
													</li>

													<li class='sbtn-dropdown-divider delete'><a class="confirm" href="<?php echo site_url('admin/nitrocart/affiliates/delete/' . $type->id); ?>"><?php echo lang('nitrocart:common:delete');?></a></li>
												</ul>

										</span>

									</span>
								</td>
							</tr>


			<?php endforeach; ?>



		</tbody>
		<tfoot>
			<tr>
				<td colspan="8"><div style="float:right;"></div></td>
			</tr>
		</tfoot>
	</table>

	<div class="buttons">
		<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
	</div>
	</div>
	</section>
<?php endif; ?>

<?php echo form_close(); ?>

<?php if (isset($pagination)): ?>
	<?php echo $pagination; ?>
<?php endif; ?>