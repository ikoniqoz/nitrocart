<section class="title">
	<h4>Report : Best Spending Customers</h4>
</section>

<section class="item">
	<div class="content">
			<div class='item' id="" style="">

				<?php echo form_open(NC_ADMIN_ROUTE.'/reports/allorders/bestcustomers'); ?>
					<input type='hidden' class='' name='reportname' value='bestcustomers'>
					Limit:<input type='text' class='' name='limit' value='10'><br/>
					<input type='hidden' class='' name='include_extra' value='0'>
					<button class="btn blue" value="View" name="btnAction" type="submit">View</button>
					<button class="btn blue" value="Download" name="btnAction" type="submit">Download</button>
				<?php echo form_close(); ?>		
			</div>


		<br />
		<br />

	<table>
		<thead>
			<tr>
				<th>User ID</th>
				<th>User name</th>	
				<th>Total Items</th>						
				<th>Total Orders</th>
				<th>Total Value</th>
			
			</tr>
		</thead>
		<tbody>


			<?php $data = new StdClass();
				foreach ($reportdata AS $key => $value):  ?>

 

							<tr class="<?php echo alternator('even', ''); ?>">
					
								<td><?php echo $value['user_id']; ?></td>
								<td><?php echo user_displayname($value['user_id'], true) ; ?></td>	
								<td><?php echo $value['total_items']; ?> </td>	
								<td><?php echo $value['order_sales']; ?></td>																		
								<td>$ <?php echo $value['orders_total']; ?></td>		
									
							</tr>


			<?php endforeach; ?>



		</tbody>
		<tfoot>
			<tr>
				<td colspan="8"><div style="float:right;"></div></td>
			</tr>
		</tfoot>
	</table>

	<div class="buttons">

	</div>

	</div>
	</section>