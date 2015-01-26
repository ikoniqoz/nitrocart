<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
<input type='hidden' name='id' value='{{id}}'>
<input type='hidden' name='product_id' value='{{product_id}}'>
<section class="title">
	<h4>Coupons</h4>
</section>

<section class="item">

	<div class="content">
	
		<ul>

			<li>
				<label>Code:<label>
				{{code}}
			</li>
			<li>
				<label>Product ID:<label>
				{{product_id}}
			</li>			
			<li>
				<label>Max use:<label>
				<input type='text' name='max_use' value='{{max_use}}'>
			</li>
			<li>
				<label>Active/Enabled:<label>
				<select name='enabled'>
					<option value='0' <?php echo ($enabled==0)?'selected':'';?>>No</option>
					<option value='1' <?php echo ($enabled==1)?'selected':'';?>>Yes</option>					
				</select>

			</li>			
			<li>
				<label>Discount:<label>
				<select name='pcent'>
					<?php for($i=0;$i<100;$i++):?>
						<?php if($pcent == $i):?>
							<option selected value='<?php echo $i;?>'><?php echo $i;?> %</option>						
						<?php else:?>
							<option value='<?php echo $i;?>'><?php echo $i;?> %</option>
						<?php endif;?>
					<?php endfor;?>
				</select>
			</li>	
			<li>
				<button class='btn blue'>Save</button>
				<a href='{{x:uri x='ADMIN'}}/coupons' class='btn gray'>Cancel</a>
			</li>						
		</ul>


		{{pagination:links}}
	
	</div>

</section>
<?php echo form_close();?>
