<div class="one_full" id="">
	<section class="title">
		    <span>
				<h4>
					<?php echo lang('nitrocart:gateways:title');?>
				</h4>
			</span>
	</section>
	<section class="item">
		<div class="content">
		<?php if (!empty($installed)): ?>
					<table>
						<thead>
							<tr>
								<th><?php echo lang('nitrocart:gateways:name'); ?></th>
								<th><?php echo lang('nitrocart:gateways:image'); ?></th>
								<th><?php echo lang('nitrocart:gateways:description'); ?></th>
								<th>Enabled</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($installed as $item): ?>
								<tr>
									<td><?php echo $item->title; ?></td>
									<td><?php echo $item->image ? img($item->image) : ''; ?></td>
									<td><?php echo $item->description; ?></td>
									<td><?php echo yesNoBOOL($item->enabled); ?></td>
									<td class="">
										<span style="float:right">
											<?php if ($item->enabled):?>
												<a class='btn red' href='{{x:uri x="ADMIN"}}/gateways/enable/<?php echo $item->id;?>/0'><?php echo lang('nitrocart:admin:disable');?></a>
											<?php else:?>
												<a class='btn orange' href='{{x:uri x="ADMIN"}}/gateways/enable/<?php echo $item->id;?>/1'><?php echo lang('nitrocart:admin:enable');?></a>
											<?php endif;?>
											<a class='btn blue' href='{{x:uri x='ADMIN'}}/gateways/edit/<?=$item->id;?>'><?= lang('nitrocart:admin:edit');?></a>
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
					<div class="no_data"><?php echo lang('nitrocart:gateways:no_installed_gateways'); ?></div>
				<?php endif; ?>			
		</div>
	</section>
</div>