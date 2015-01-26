<div class="one_half" id="">
	<section class="title">
	    <span>
			<h4><?php echo lang('nitrocart:tax:title');?></h4>
		</span>
	</section>

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

	<section class="item">
		<div class="content">

			<?php if (empty($taxes)): ?>
				<div class="no_data">
				<p>
					<?php echo lang('nitrocart:tax:no_data');?>
				</p>
				<?php echo lang('nitrocart:tax:no_data_create');?>
				
				</div>
			<?php endif; ?>

			<table>
				<thead>
					<tr>
						<th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
						<th>ID</th>
						<th>Name</th>
						<th>Rate</th>
						<th>Is Default</th>
						<th style="width: 120px"></th>
					</tr>
				</thead>
				<tbody>
						<tr>
							<td></td>
							<td></td>
							<td>None</td>
							<td>0 %</td>
							<td>NULL</td>
							<td>
							</td>
						</tr>

					<?php foreach ($taxes AS $tax): ?>
						<tr>
							<td><input type="checkbox" name="action_to[]" value="<?php echo $tax->id; ?>"  /></td>
							<td><?php echo $tax->id; ?></td>
							<td><?php echo $tax->name; ?></td>
							<td><?php echo $tax->rate; ?> %</td>
							<td><?php echo yesNoBOOL($tax->default, 'string', '<span style="color:green">Yes</span>','<span style="color:gray">No</span>'); ?></td>
							<td>

							<?php

								$items = array();

								$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/tax/edit/{$tax->id}", false,  "Edit", false, "eye-open");
								$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/tax/delete/{$tax->id}", true,  "Delete", true, "minus");


								echo dropdownMenuList($items,'');

								?>

							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
			<div class="buttons">
				<?php $this->load->view('admin/partials/buttons', array('buttons' => array('delete'))); ?>
			</div>

		</div>
	</section>
</div>


<div class="one_half last" id="">
	<section class="title">
	    <span>
			<h4></h4>
		</span>
	</section>
	<section class="item">
		<div class="content">
		</div>
	</section>
</div>

<?php echo form_close(); ?>
<?php if (isset($pagination)): ?>
	<?php echo $pagination; ?>
<?php endif; ?>