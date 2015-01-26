<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>

<section class="title">
	<h4>Coupons</h4>
</section>

<section class="item">

	<div class="content">
	
		<ul>
			<li>
				<label>Code:<label>
				<input type='text' name='code'>
			</li>
			<li>
				<label>Product ID:<label>
				<input type='text' name='product_id'>
			</li>			
			<li>
				<label>Max use:<label>
				<input type='text' name='max_use'>
			</li>
			<li>
				<label>Active/Enabled:<label>
				<select name='enabled'>
					<option value='0'>No</option>
					<option value='1'>Yes</option>					
				</select>
			</li>				
			<li>
				<label>Discount:<label>
				<select name='pcent'>
					<?php for($i=0;$i<100;$i++):?>
						<option value='<?php echo $i;?>'><?php echo $i;?> %</option>
					<?php endfor;?>
				</select>
			</li>	
			<li>
				<button class='btn blue'>Create</button>
			</li>						
		</ul>

		{{pagination:links}}
	
	</div>

</section>
<?php echo form_close();?>
