<div id="">
	<div class="" id="">
		<section class="title">
			<h4><?php echo lang('nitrocart:packages:title'); ?></h4>
			<span style="float:right;margin-right:10px;">
			</span>
		</section>
		<section class="item">
			<div class="content">

						<div id="installed" class="">
							<fieldset>
									<?php if (!empty($packages)): ?>
										<table>
											<thead>
												<tr>
													<th><?php echo lang('nitrocart:packages:name'); ?></th>
													<th><?php echo lang('nitrocart:packages:inner_outer_height'); ?></th>
													<th><?php echo lang('nitrocart:packages:inner_outer_width'); ?></th>
													<th><?php echo lang('nitrocart:packages:inner_outer_length'); ?></th>
													<th><?php echo lang('nitrocart:packages:empty_max_weight'); ?></th>
													<th>Code</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($packages as $item): ?>
													<tr>
														<td><?php echo $item->name; ?></td>
														<td><?php echo $item->height .'/'. $item->outer_height; ?> [cm]</td>
														<td><?php echo $item->width.'/'. $item->outer_width; ?> [cm]</td>
														<td><?php echo $item->length.'/'. $item->outer_length; ?> [cm]</td>
														<td><?php echo $item->cur_weight.' / '.$item->max_weight; ?> [kg]</td>
														<td><?php echo $item->code; ?></td>

														<td class="">
																<span style="float:right;">
																	<?php
																		$items = array();
																		$items[] = dropdownMenuStandard(site_url(NC_ADMIN_ROUTE.'/packages/edit/'.$item->id), false, lang('nitrocart:packages:edit'), false, "edit");																	
																		$items[] = dropdownMenuStandard(site_url(NC_ADMIN_ROUTE.'/packages/duplicate/'.$item->id), false, lang('nitrocart:packages:copy'), false, "edit");
																		$items[] = dropdownMenuStandard(site_url(NC_ADMIN_ROUTE.'/packages/duplicate/'.$item->id.'/edit'), false, lang('nitrocart:packages:copy_edit'), false, "edit");
																		$items[] = dropdownMenuStandard(site_url(NC_ADMIN_ROUTE.'/packages/delete/' . $item->id), true,  lang('nitrocart:packages:delete'), true, "minus");
																		echo dropdownMenuList($items,'Actions');
																	?>
																</span>

														</td>
													</tr>
												<?php endforeach; ?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="7">
														<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
													</td>
												</tr>
											</tfoot>
										</table>
									<?php else: ?>
										<div class="no_data"><?php echo lang('nitrocart:packages:no_packages_here');?></div>
									<?php endif; ?>

									<?php echo form_close(); ?>
							</fieldset>
						</div>

			</div>
		</section>
	</div>

</div>