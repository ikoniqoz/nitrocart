<section class="title">
	<h4>Report : Best Sellers / By Date Range</h4>
</section>

<section class="item">
	<div class="content">
			<div class='item' id="" style="">

				<?php echo form_open(NC_ADMIN_ROUTE.'/reports/allorders/highorders'); ?>
					<input type='hidden' class='' name='reportname' value='highorders'>
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
				<th>Order ID</th>
				<th>Order Date</th>								
				<th>Value</th>
				<th>Total Items</th>
				<th></th>				
				<th></th>				
			</tr>
		</thead>
		<tbody>


			<?php $data = new StdClass();
				foreach ($reportdata AS $key => $value):  ?>

							<tr class="<?php echo alternator('even', ''); ?>">
								<td><a target='new' href='{{x:uri x='ADMIN'}}/orders/order/<?php echo $value['id']; ?>' class='button'><?php echo $value['id']; ?></a></td>
								<td><?php echo format_date($value['order_date']); ?></td>		
								<td><?php echo $value['total_totals']; ?></td>																		
								<td><?php echo $value['count_items']; ?></td>		
								<td></td>		
								<td><a href='{{x:uri x='ADMIN'}}/orders/order/<?php echo $value['id']; ?>' class='button blue'>View Order</a></td>								
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