<section class="title">
	<h4>Product Types</h4>
</section>
<?php echo form_open_multipart(NC_ADMIN_ROUTE.'/variances_types/mdelete', 'class="crud"'); ?>
<section class="item">
	<div class="content">
			<fieldset id="">
			</fieldset>
	<table>
		<thead>
			<tr>
				<th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
				<th>ID</th>
				<th>Name</th>
				<th>Custom Layout</th>
				<th>Created</th>
				<th>Is Default</th>
				<th style="width: 120px"></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($entries ) >0): ?>
			<?php
				foreach ($entries AS $type): ?>
							<tr class="<?php echo alternator('even', ''); ?>">
								<td><input type="checkbox" name="action_to[]" value="<?php echo $type['id']; ?>"  /></td>
								<td><?php echo $type['id']; ?></td>
								<td><?php echo $type['name']; ?></td>
								<td><span style='font-family:courier'><?php echo 'shop_type_'.$type['slug'].'.html';?></span></td>
								<td><?php echo nc_format_date($type['created']); ?></td>
								<td><?php echo yesNoBOOL($type['default'], 'string', '<span style="color:green">Yes</span>','<span style="color:gray">No</span>'); ?></td>

								<td>
									<span style="float:right;">
												<?php
													$items = array();
													$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE.'/products_types/edit/' . $type['id'], false, lang('nitrocart:admin:edit'), false, "edit");
													$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE.'/products_types/delete/' . $type['id'], 	true,  lang('nitrocart:admin:delete'), true, "minus");
													echo dropdownMenuList($items,'Actions');
												?>
									</span>
								</td>
							</tr>
			<?php endforeach; ?>
			<?php else: ?>
							<tr>
								<td colspan="9">
								No recoreds found
								</td>
							</tr>
			<?php endif; ?>

		</tbody>
		<tfoot>
			<tr>
				<td colspan="8"><div style="float:right;"></div></td>
			</tr>
		</tfoot>
	</table>
	<div class="buttons">

	</div>
	</div>
	</section>
<?php echo form_close(); ?>
<?php if (isset($pagination)): ?>
	<?php echo $pagination; ?>
<?php endif; ?>