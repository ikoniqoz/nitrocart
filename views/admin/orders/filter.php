<div class='item' id="filters_group" style="">

	<fieldset id="filters" style="display:block;">
	<?php echo form_open(NC_ADMIN_ROUTE.'/orders/callback'); ?>

		<div class='item one_half' style="float:left;width:auto;">	

			<div class="input"  style="float:right;">
				 <?php echo form_input('f_keyword_search',  $f_keyword_search, 'placeholder="Order #" style="width:215px;height: 20px; padding: 3px 10px;"'); ?>
					<a title='Clear Search' style='float:none;padding:15px;' class='img_icon img_delete tooltip-s' href='{{x:uri x='ADMIN'}}/orders/filter/clear'></a>
					<a title='Refine Search' style='float:none;padding:15px;' class='img_icon img_filter tooltip-s'  id="flink" href="javascript:toggle_filter()" ></a>	
			</div>

			<div>
				<div class="input">
					<?php echo form_dropdown('f_filter', $filters , $namespace ); ?>
				</div>
			</div>

		</div>
		<div class='item two_thirds last' id="hideable_filters" style="display:none;">	
							

			<div class='one_third' id="" style="">
				<ul>
						<li>
							<label>
								Order By
							</label>
							<div class="input">
								
										<?php echo form_dropdown('f_order_by',  
											array(
												'id'=> 'ID',
												'status'=> 'Status',
												'created'=> 'Date Placed',
												'paid_date'=> 'Date Paid',
												'total_amount_order_wt' => 'Total $',
												),$f_order_by ); ?>
							</div>
						</li>

						<li>
							<label>
								Order Direction
							</label>
							<div class="input">
									<?php echo form_dropdown('f_order_by_dir',  array('desc'=> 'Descending','asc'=> 'Ascending',),$f_order_by_dir ); ?>
							</div>
						</li>	
						<li>
							<label>
								Items per Page
							</label>
							<div class="input">
								<?php echo form_dropdown('f_display_count', array( 5  => '5', 10 => '10',	 20 => '20', 50 => '50', 100 => '100', 150 => '150', 200 => '200', ),$f_display_count ); ?>
							</div>
						</li>												
				</ul>	
			</div>			
			<div class='one_third' style='float:right'>	
				<ul>
							<li>
							<label>
								View Mode
							</label>
							<div class="input">
								<?php echo form_dropdown('f_status',  array('all'=> lang('global:select-all'),'active'=> 'Active','deleted'=> 'Deleted'), $f_status ); ?>
							</div>
						</li>
						<li>
							<label>
								Payment Status
							</label>
							<div class="input">
								<?php echo form_dropdown('f_payment_status', array( 'all'=> lang('global:select-all'),'paid'  => 'Paid','unpaid' => 'UnPaid', ),$f_payment_status ); ?>
							</div>
						</li>	

						<li>
							<label>
								Order Status
							</label>
							<div class="input">
								<?php echo form_dropdown('f_order_status',$order_workflows,$f_order_status);?>
							</div>
						</li>	



				</ul>
			</div>
		</div>
	<?php echo form_close(); ?>		
	</fieldset>
</div>