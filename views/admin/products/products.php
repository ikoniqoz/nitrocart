<section class="title">
	    <span style="">
			<h4>Manage <?php echo lang('nitrocart:products:products');?></h4>
		</span>
</section>
<section class="item">
	<div class="content">

		<?php $this->load->view('admin/products/filter'); ?>

		<?php if ($products) : ?>

			<div style="clear:both"></div>
			<?php echo form_open(NC_ADMIN_ROUTE.'/products/action'); ?>


								<table class="thin">

									<thead>
										<tr>
											<th class="collapse"><?php echo form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
											<th class="collapse"><?php echo lang('nitrocart:products:id');?></th>
											<th class="collapse"><?php echo lang('nitrocart:products:image');?></th>
											<th class="collapse"><?php echo lang('nitrocart:products:name');?></th>
											<?php 
											if ($this->config->item('admin/show_products_views_field'))
											{
												echo '<th>Views</th>';
											}
											?>	
											<?php 
											if ($this->config->item('admin/show_products_featured_field'))
											{
												echo "<th class='collapse'><span class='tooltip-s'  title='Is this product featured ?'>Featured</span></th>"; 
											}
											?>
											<th class="collapse"><span class='tooltip-s' title='Can users find this product in search results ?'><?php echo lang('nitrocart:products:searchable');?></span></th>
											<th class="collapse"><span class='tooltip-s' title='Is this product currently available on the site ?'>Enabled</span></th>
											<th class="collapse"><span class='tooltip-s' title='Is the product active or deleted from history ?'></span></th>
											<th></th>
										</tr>
									</thead>
									<tbody id="filter-stage">

									</tbody>
							</table>
			<?php echo form_close(); ?>
		<?php else : ?>
			<div class="no_data">
				No results found here, try a different filter or create a new product.
			</div>
		<?php endif; ?>
	</div>
</section>



