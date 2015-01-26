<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<section class="title">
		<h4><?php echo lang('nitrocart:customers:customer');?></h4>
</section>

<section class="item">
	<div class="content">

				<fieldset>
					<ul>

						<li class="even">
							<label for="">
								Users Group Level : <?php echo $user->first_name.' '.$user->last_name;?>
							</label>
							<div class="input">
								<?php echo form_dropdown('group_id',$pyroUserGroups,set_value( 'group_id', $user->group_id ));?>
								<input type='hidden' name='user_id' value='<?php echo $user->user_id;?>'>
							</div>

							<button class="btn blue" value="Update" name="btnAction" type="submit">
									<span>Update &amp; Exit</span>
							</button>


							<a class='btn gray' href='{{x:uri x='ADMIN'}}/customers/'>Cancel</a>

					</ul>
				</fieldset>
			
	</div>
</section>
<?php echo form_close(); ?>