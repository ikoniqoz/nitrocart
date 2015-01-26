<section class="title">
	<h4>Report : Products Sold / By Date Range</h4>
</section>

<section class="item">
	<div class="content">
			<div class='item' id="" style="">

				<?php echo form_open(NC_ADMIN_ROUTE.'/reports/daterange/prodsold'); ?>
					<input type='hidden' class='' name='reportname' value='prodsold'>
					<?php $this->load->view('admin/reports/filters/bydate'); ?>
				<?php echo form_close(); ?>		
			</div>


		<br />
		<br />

	<table>
		<thead>
			<tr>
				<th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
				<th>Product ID</th>
				<th>Variant ID</th>								
				<th>Product Name</th>
				<th>Item Value</th>
				<th>Total Sales</th>				
				<th>Total Value</th>				
			</tr>
		</thead>
		<tbody>


			<?php $data = new StdClass();
				foreach ($reportdata AS $key => $value):  ?>

							<tr class="<?php echo alternator('even', ''); ?>">
								<td><input type="checkbox" name="action_to[]" value="<?php echo $key; ?>"  /></td>
								<td><?php echo $value['product_id']; ?></td>		
								<td><?php echo $value['variant_id']; ?></td>																		
								<td><a class='' href='{{x:uri x='ADMIN'}}/product/edit/<?php echo $value['product_id']; ?>'><?php echo $value['title']; ?> - <em><?php echo nc_variant_name($value['variant_id']); ?></em></a></td>
								<td><span class='stags orange'> $<?php echo $value['item_amount']; ?></span> </td>
								<td><?php echo $value['total_qty']; ?></td>								
								<td><?php echo $value['total_return']; ?></td>								
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