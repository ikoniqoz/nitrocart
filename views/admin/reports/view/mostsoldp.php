<section class="title">
	<h4>Report : Best Sellers / By Date Range</h4>
</section>

<section class="item">
	<div class="content">
			<div class='item' id="" style="">

				<?php echo form_open(NC_ADMIN_ROUTE.'/reports/daterange/mostsoldp'); ?>
					<input type='hidden' class='' name='reportname' value='mostsoldp'>
					<?php $this->load->view('admin/reports/filters/bydate'); ?>
				<?php echo form_close(); ?>		
			</div>

		<br />
		<br />

	<table>
		<thead>
			<tr>
				<th>Product ID</th>
				<th>Product</th>								
				<th>Variant Name</th>
				<th>Item Value</th>
				<th>Total Sales</th>				
				<th>Total Value</th>
				<th></th>					
			</tr>
		</thead>
		<tbody>

			<?php $data = new StdClass();
				foreach ($reportdata AS $key => $value):  ?>

							<tr class="<?php echo alternator('even', ''); ?>">
								<td><a target='new' href='{{x:uri x='ADMIN'}}/product/edit/<?php echo $value['product_id']; ?>' class='button'><?php echo $value['product_id']; ?></a></td>	
								<td><a target='new' href='{{x:uri x='ADMIN'}}/product/edit/<?php echo $value['product_id']; ?>' class=''><?php echo $value['title']; ?></a></td>																			
								<td><em><a target='new' href='{{x:uri x='ADMIN'}}/product/variant/<?php echo $value['variant_id']; ?>' class=''><?php echo nc_variant_name($value['variant_id']); ?></a></em></td>										
								<td><span class='stags orange'> $<?php echo $value['item_amount']; ?></span> </td>
								<td><?php echo $value['total_qty']; ?></td>								
								<td><?php echo $value['total_return']; ?></td>	
								<td>
									<a class='button blue' href='{{x:uri x='ADMIN'}}/product/edit/<?php echo $value['product_id']; ?>'>View product</a>
								</td>							
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