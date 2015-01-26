
<div class="one_full" id="">
	<section class="title">
		    <span>
				<h4>Shipping Options</h4>
			</span>
	</section>
	<section class="item">
		<div class="content">
						<?php if (!empty($installed)): ?>
									<table>
										<thead>
											<tr>
												<th><?php echo lang('nitrocart:shipping:name'); ?></th>
												<th><?php echo lang('nitrocart:shipping:image'); ?></th>
												<th><?php echo lang('nitrocart:shipping:description'); ?></th>
												<th>Enabled</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($installed as $item): ?>
												<tr>
													<td><?php echo $item->title; ?></td>
													<td><?php echo $item->image ? img($item->image) : ''; ?></td>
													<td><?php echo wordwrap($item->description, 50, "<br />\n"); ?></td>
													<td><?php echo yesNoBOOL($item->enabled); ?></td>
													<td class="">
														<span style="float:right">

															<a class='btn blue' href='{{x:uri x='ADMIN'}}/shipping/edit/<?=$item->id;?>'>Edit</a>

																<?php
																	$items = array();
																	$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/shipping/edit/{$item->id}", false, lang('nitrocart:shipping:edit'), false, "edit");
																	if ($item->enabled){
																		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/shipping/disable/{$item->id}", false, lang('nitrocart:shipping:disable'), false, "edit");
																	}
																	else {
																		$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/shipping/enable/{$item->id}", false, lang('nitrocart:shipping:enable'), false, "edit");
																	}							
																	$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/shipping/uninstall/{$item->id}", true,  lang('nitrocart:shipping:uninstall'), true, "minus");
																	echo dropdownMenuList($items,'More');
																?>

															</span>
														</span>

													</td>
												</tr>
											<?php endforeach; ?>
										</tbody>
										<tfoot>
											<tr>
												<td colspan="5">
													<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
												</td>
											</tr>
										</tfoot>
									</table>
								<?php else: ?>
									<div class="no_data"><?php echo lang('nitrocart:shipping:no_installed'); ?></div>
								<?php endif; ?>
		</div>
	</section>
</div>


<div class="one_full" id="">
	<section class="title">
		    <span>
				<h4>Available Shipping Options</h4>
			</span>
	</section>
	<section class="item">
		<div class="content">
			<fieldset>
					<?php if (!empty($uninstalled)): ?>

						<table>
							<thead>
								<tr>
									<th><?php echo lang('nitrocart:shipping:name'); ?></th>
									<th><?php echo lang('nitrocart:shipping:image'); ?></th>
									<th><?php echo lang('nitrocart:shipping:description'); ?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($uninstalled as $item): ?>
									<tr>
										<td><?php echo $item->name; ?></td>
										<td><?php echo $item->image ? img($item->image) : ''; ?></td>
										<td><?php echo $item->description; ?></td>
										<td class="actions">
											<?php echo anchor(NC_ADMIN_ROUTE.'/shipping/install/' . $item->slug, lang('global:install'), 'class="button"'); ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="4">
										<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
									</td>
								</tr>
							</tfoot>
						</table>

					<?php else: ?>
						<div class="no_data"><?php echo lang('nitrocart:shipping:no_data'); ?></div>
					<?php endif; ?>

			</fieldset>
		</div>
	</section>
</div>

