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
					<?php echo form_dropdown('max_allowed',
					[
						'100'=>'100',
						'100000'=>'100,000',
						'500000'=>'500,000',
						'1000000'=>'1,000,000'
					],$max_allowed);?>					
				</td>
			</tr>			
			<tr>
				<td>
					<label>Enabled:<label>
				</td>
				<td>							
					<?php echo form_dropdown('enabled',[0=>'No',1=>'Yes'],$enabled);?>		
				</td>
			</tr>			
			<tr>
				<td>				
					<button class='btn blue'>Save</button>
				</td>
				<td>					
					<a href='{{x:uri x='ADMIN'}}/apis' class='btn gray'>Cancel</a>
				</td>
			</tr>						
		</table>
	</div>
</section>
<?php echo form_close();?>
