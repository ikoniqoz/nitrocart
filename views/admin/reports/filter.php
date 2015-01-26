<div class='item' id="" style="">


	<?php echo form_open(NC_ADMIN_ROUTE.'/reports/view'); ?>

		<div class='item one_full' style="float:left;width:auto;">	

			<div class="input">

					<div class="input">
							Limit:<br/><input type='text' class='' name='limit' value='10'><br/><br/>
							<input type='hidden' class='' name='reportname' value='orderbydate'>
							<input type='hidden' class='' name='include_extra' value='0'>
							Start:<br/><input type='text' class='datepicker' name='date_start'><br/><br/>
							End:<br/><input type='text' class='datepicker' name='date_end'><br/>
					</div>

					<input type='submit' name='btnAction' value='View' class='button blue'>
					<input type='submit' name='btnAction'  value='Download' class='button blue'>
								
			</div>

		</div>

	<?php echo form_close(); ?>		

</div>