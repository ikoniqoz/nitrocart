<section class="title">
	<h4><?php echo lang('nitrocart:workflows:title');?></h4>
</section>
<?php echo form_open(NC_ADMIN_ROUTE.'/workflows/delete'); ?>
<section class="item">
	<div class="content">
	<?php if (empty($workflows)): ?>
		<div class="no_data">
			<p>
				<?php echo lang('nitrocart:workflows:workflows');?>
			</p>
				<?php echo lang('nitrocart:workflows:no_data');?>
			<br /><br /><br />
			<p>
			<small>
				<?php echo lang('nitrocart:admin:feedback');?>
			</small>
			</p>
		</div>
</section>
<?php else: ?>
	<table class='sortable' id='sortable_list'>
		<thead>
			<tr>
				<th></th>
				<th><?php echo lang('nitrocart:admin:id');?></th>
				<th><?php echo lang('nitrocart:workflows:workflow');?></th>
				<th><?php echo lang('nitrocart:workflows:progress');?></th>
				<th>Default</th>
				<th style="width: 120px"></th>
			</tr>
		</thead>
		<tbody>

			<?php
				$data = new StdClass();
				foreach ($workflows AS $workflow): ?>
				<?php $_items = array();?>
				<?php $data->workflow = $workflow; ?>
				<tr>
					<td><a class='handle'></a> <input type="checkbox" name="action_to[]" value="<?php echo $workflow->id; ?>"  /></td>
					<td><?php echo $workflow->id; ?></td>
					<td><?php echo $workflow->name; ?></td>
					<td><?php echo $workflow->pcent; ?> %</td>
					<td><?php echo $workflow->is_placed; ?></td>
					<td>
						<span style="float:right;">
							<a href="{{x:uri x='ADMIN'}}/workflows/edit/<?php echo $workflow->id;?>" class='button edit_button'><?php echo lang('nitrocart:admin:edit');?></a>
							<?php if($workflow->core == 0):?>
								<a href="{{x:uri x='ADMIN'}}/workflows/delete/<?php echo $workflow->id;?>" class='button delete_button'><?php echo lang('nitrocart:admin:delete');?></a>
							<?php endif;?>
						</span>
					</td>
				</tr>
			<?php endforeach; ?>

		</tbody>
		<tfoot>
			<tr>
				<td colspan="5"><div style="float:right;"></div></td>
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