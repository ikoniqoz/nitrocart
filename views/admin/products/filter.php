<div class='item' id="filters_group" style="">

	<fieldset id="filters" style="">
	<?php echo form_open(NC_ADMIN_ROUTE.'/products/callback'); ?>

		<div class='item one_third' style="float:left;width:auto;">	
			<div class="input"  style="float:right;">
				 <?php echo form_input('f_keyword_search',  $f_keyword_search, 'placeholder="Search" style="width:215px;height: 20px; padding: 3px 10px;"'); ?>
					<a title='Clear Search' style='float:none;padding:15px;' class='img_icon img_delete tooltip-s' href='{{x:uri x='ADMIN'}}/products/filter/clear'></a>
					<a title='Refine Search' style='float:none;padding:15px;' class='img_icon img_filter tooltip-s'  id="flink" href="javascript:toggle_filter()" ></a>	

					<div class="input">
						<?php //echo form_dropdown('f_filter', $filters , $namespace ); ?>
						<?php echo $filter_type; ?>

					</div>

			</div>		
		</div>

		<div class='item two_thirds last' id="hideable_filters" style="display:none;">	
			<div class='one_third' style=''>	
				<?php echo form_hidden('f_module', $module_details['slug']); ?>		
					<ul>  
						<li>
							<label>
								<?php echo lang('nitrocart:products:order_by'); ?>
							</label>
							<div class="input">

								<?php echo form_dropdown('f_order_by',  
										array(
											'id'=> 'ID',
											'name'=> 'Name',
											'slug'=> 'Slug',
											'views'=> 'View Count',
											'created'=> 'Date Created',
											'updated'=> 'Date Last Updated',
											'ordering_count' => 'Custom Order',
											'created_by' => 'User that Created Product',
											),$f_order_by ); ?>
							</div>
						</li>	
						<li>
							<label>
								Order Direction
							</label>
							<div class="input">

								<?php echo form_dropdown('f_order_by_dir',  
										array(
											'asc'=> 'Ascending',
											'desc'=> 'Decending',
											),$f_order_by_dir ); ?>
							</div>
						</li>	
						<li>
							<label>
								<?php echo lang('nitrocart:products:items_per_page'); ?>
							</label>
							<div class="input">
								<?php echo form_dropdown('f_items_per_page', array(5=>"5", 10=>"10", 20=>"20", 50=>"50", 100=>"100", 200=>"200"), $f_items_per_page); ?>
							</div>
						</li>		
					</ul>
			</div>
			<div class='one_third' style='float:right;'>	
					<ul> 			
						<li>
							<label>
								Enabled On/Off
							</label>
							<div class="input">
								<?php echo form_dropdown('f_visibility',  array('all' => lang('global:select-all'),'on'=> 'On / Visible','off'=> 'Off / InVisible'), $f_visibility ); ?>
							</div>
						</li>
						<li>
							<label>
								Featured
							</label>
							<div class="input">
								<?php echo form_dropdown('f_featured',  array('all'=> lang('global:select-all'),'yes'=> 'Yes','no'=> 'No'), $f_featured ); ?>
							</div>
						</li>							
						<li>
							<label>
								Status
							</label>
							<div class="input">
								<?php echo form_dropdown('f_status',  array('all'=> lang('global:select-all'),'active'=> 'Active','deleted'=> 'Deleted'), $f_status ); ?>
							</div>
						</li>						
					</ul>
			</div>
		</div> 
	
	<?php echo form_close(); ?>		
	</fieldset>
</div>