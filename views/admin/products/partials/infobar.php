<fieldset style='' id='info_bar'>

	<div id="cover_container" class="input" style="float:left">
		<?php  echo "<div>".nc_product_cover( $id,true, 'prod_cover' )."</div>"; ?>
	</div>

	<div style="float:right;">

			<ul style="float:none;vertical-align:bottom;text-align:right">
				<li>
			    <span style="">
			        <a title='Previous Product' href="{{x:uri x='ADMIN'}}/product/autosel/{{userDisplayMode}}/prev/{{id}}" class='small btn gray rounded tooltip-s'>&larr;</a>
			        <a title='Next Product' href="{{x:uri x='ADMIN'}}/product/autosel/{{userDisplayMode}}/next/{{id}}" class='small btn gray rounded tooltip-s'>&rarr;</a>
			    </span>
				</li>
				<li>

					<?php if($deleted == NULL):?>
						<span class="sbtn-dropdown" data-buttons="dropdown" style='margin-top:20px;'>

								<a href="#" class="btn green">
									<?php echo lang('nitrocart:products:more');?> <i class="icon-caret-down"></i>
								</a>

								<!-- Dropdown Below Button -->
								<ul class="sbtn-dropdown">
									<?php if($this->method=='edit'):?>
										<li class='sbtn-dropdown-divider'><a class="" href="{{x:uri x='ADMIN'}}/product/view/{{id}}">Switch to View Mode</a></li>
									<?php else:?>
										<li class='sbtn-dropdown-divider'><a class="" href="{{x:uri x='ADMIN'}}/product/edit/{{id}}">Switch to Edit Mode</a></li>
									<?php endif;?>
										<li class='sbtn-dropdown-divider'><a class="" target ='new' href="{{x:uri}}/products/product/{{slug}}">View as User</a></li>
										<li class='sbtn-dropdown-divider'><a class="" target ='new' href="{{x:uri}}/productsadmin/product/{{slug}}/1">View as Admin</a></li>

									<?php if($this->method=='edit'):?>
										<li class='sbtn-dropdown-divider delete'><a class="confirm" href="{{x:uri x='ADMIN'}}/product/delete/{{id}}">Delete</a></li>
									<?php endif;?>										
								</ul>

						</span>
						<?php endif;?>


					<div style='padding:12px;padding-right:0px;'>

					<ul>
						<li>
					<?php if($deleted != NULL):?>
							<span class='stags red'>Deleted:<?php echo nc_format_date($deleted,'hms'); ?></span>			
					<?php endif;?>						
							<span class='stags gray'><?php echo lang('nitrocart:products:date_created');?>:<?php echo nc_format_date($created,'hms'); ?></span>
							<span class='stags blue'><?php echo lang('nitrocart:products:date_updated');?>:<?php echo nc_format_date($updated,'hms'); ?></span>
						</li>

					</ul>
					</div>
				</li>											
			</ul>
	</div>
</fieldset>