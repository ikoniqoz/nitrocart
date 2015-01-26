

					<div class='item one_full' style="float:left;width:auto;">	
						<div class="input">
								<div class="input">
										Limit:<br/><input type='text' class='' name='limit' value='10'><br/><br/>
										<input type='hidden' class='' name='include_extra' value='0'>
										Start:<br/><?php echo form_input('date_start',$date_start,'class="datepicker"');?><br/><br/>
										End:<br/><?php echo form_input('date_end',$date_end,'class="datepicker"');?><br/><br/>
								</div>
				
								<button class="btn blue" value="View" name="btnAction" type="submit">View</button>
								<button class="btn blue" value="Download" name="btnAction" type="submit">Download</button>
		
						</div>
					</div>