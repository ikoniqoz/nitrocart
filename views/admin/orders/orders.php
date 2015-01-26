<section class="title">
	    <span id='nc_logo' style=''></span>
	    <span style="float:left;">
			<h4><?php echo lang('nitrocart:orders:title');?></h4>
		</span>
</section>
<section class="item">
	<div class="content">
		<?php $this->load->view('admin/orders/filter'); ?>
		<?php if ($orders) : ?>
			<div style="clear:both"></div>
			<?php echo form_open(NC_ADMIN_ROUTE.'/orders/action'); ?>
				<table class="thin">
					<thead>
						<tr>
							<th class="collapse"><?php echo lang('nitrocart:orders:id');?></th>
							<th class="collapse"></th>
							<th class="collapse"><?php echo lang('nitrocart:orders:customer');?></th>
							<th class="collapse"><?php echo lang('nitrocart:orders:date');?></th>
							<th class="collapse"><?php echo lang('nitrocart:orders:total');?></th>
							<th class="collapse"><span class='tooltip-s' title=''><?php echo lang('nitrocart:orders:status');?></span></th>
							<th class="collapse"><span class='tooltip-s' title=''></span></th>
							<th></th>
						</tr>
					</thead>
					<tbody id="filter-stage">
					</tbody>
			</table>
		<?php echo form_close(); ?>
		<?php else : ?>
			<div class="no_data">
				<?php echo lang('nitrocart:orders:no_data');?>
			</div>
		<?php endif; ?>
	</div>
</section>
