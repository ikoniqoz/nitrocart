<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

<div id="sortable">

<div class="one_full" id="">

	<section class="title">
		<h4><?php echo lang('nitrocart:manage:title'); ?></h4>
		<a class="" title=""></a>
	</section>

	<section class="item">

		<div class="content">

			<table>
		        <?php if(ENVIRONMENT == PYRO_DEVELOPMENT):?>
				<tr>
					<td>
						<p>
							NitroCart Route<br/>
						</p>
						<p>
							<small>Be careful doing this. Your website URI will be changed permanently.</small>
						</p>								
					</td>
					<td class='input'>						
						{{url:site}}<input name='nc_route' type='text' value='<?php echo NC_ROUTE;?>'><br>
					</td>
					<td class='input'>							
						<button class="btn red" value="Create" name="btnAction" type="submit">
									<span>Create &amp; Update</span>
						</button>
					</td>
				</tr>	


				<?php endif;?>

				<tr>
					<td>
						<p>
							Rebuild Routes File<br/>
						</p>
						<p>
							<small>Rebuilds the route file from the DB</small>
						</p>								
					</td>
					<td class='input'>
					</td>						
					<td class='input'>						
						<a class='btn orange' href='{{x:uri x='ADMIN'}}/routes/buildroutes'>Rebuild</a>
					</td>
				</tr>	

				<tr>
					<td>
						<p>
							Snapshop<br/>
						</p>
						<p>
							<small>Take a snapshot of the global routes file</small>
						</p>								
					</td>
					<td class='input'>
					</td>						
					<td class='input'>						
						<a class='btn orange' href='{{x:uri x='ADMIN'}}/routes/snapshot'>Capture</a>
					</td>
				</tr>	

			</table>

			<br/>

			<div class=""></div>
		</div>

	</section>

</div>

</div>
<style>
ul.set_menu li.set_menu
{
	margin:15px;
	border-bottom: 1px solid #ccc;
	margin-bottom:25px;
	margin-top:25px;
	padding:20px;
}
</style>
<?php echo form_close(); ?>