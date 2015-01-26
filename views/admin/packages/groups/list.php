<div id="">
	<div class="" id="">
		<section class="title">
			<h4>Package Groups</h4>
			<span style="float:right;margin-right:10px;">
			</span>
		</section>
		<section class="item">
			<div class="content">

						<div id="installed" class="">
							<fieldset>
									<?php if (!empty($packages_groups)): ?>
										<table>
											<thead>
												<tr>
													<th><?php echo lang('nitrocart:packages:name'); ?></th>
													<th>Created By</th>
													<th>Created</th>
													<th>Is Default</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($packages_groups as $item): ?>
													<tr>
														<td><?php echo $item->name; ?></td>
														<td><?php echo user_displayname($item->created_by, true); ?></td>
														<td><?php echo nc_format_date($item->created,''); ?></td>

														<td><?php echo yesNoBOOL($item->default, 'string', '<span style="color:green">Yes</span>','<span style="color:gray">No</span>'); ?></td>

														<td class="">
																<span style="float:right;">

																	<?php
																		$items = array();
																		$items[] = dropdownMenuStandard(site_url(NC_ADMIN_ROUTE.'/packages_groups/edit/'.$item->id), false, lang('nitrocart:packages:edit'), false, "edit");																	
																		$items[] = dropdownMenuStandard(site_url(NC_ADMIN_ROUTE.'/packages_groups/duplicate/'.$item->id), false, lang('nitrocart:packages:copy'), false, "edit");
																		$items[] = dropdownMenuStandard(site_url(NC_ADMIN_ROUTE.'/packages_groups/delete/' . $item->id), true,  lang('nitrocart:packages:delete'), true, "minus");
																		echo dropdownMenuList($items,'Actions');
																	?>

																</span>

														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="6">
														<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
													</td>
												</tr>
											</tfoot>
										</table>
									<?php else: ?>
										<div class="no_data"><?php echo lang('nitrocart:packages:no_packagegroup_here');?></div>
									<?php endif; ?>

									<?php echo form_close(); ?>
							</fieldset>
						</div>

			</div>
		</section>
	</div>

</div>