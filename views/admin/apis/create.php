<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<section class="title">
	<h4>API Keys</h4>
</section>

<section class="item">
	<div class="content">
		<table>
			<tr>
				<td>
					<label>Name:<label>
				</td>
				<td>
					<input type='text' name='name' value='{{name}}'>
				</td>
			</tr>						
			<tr>
				<td>
					<label>Max Allowed:<label>
				</td>
				<td>						
					<select name='max_allowed'>
						<option value='100'>100</option>
						<option value='100000'>100,000</option>	
						<option value='500000'>500,000</option>	
						<option value='1000000'>1,000,000</option>																	
					</select>
				</td>
			</tr>			
			<tr>
				<td>
					<label>Enabled:<label>
				</td>
				<td>							
					<select name='enabled'>
						<option value='0'>No</option>
						<option value='1'>Yes</option>					
					</select>
				</td>
			</tr>			
			<tr>
				<td>				
					<button class='btn blue'>Save</button>
				</td>
				<td>					
					<a href="{{x:uri x='ADMIN'}}/apis" class='btn gray'>Cancel</a>
				</td>
			</tr>						
		</table>
	</div>
</section>
<?php echo form_close();?>
