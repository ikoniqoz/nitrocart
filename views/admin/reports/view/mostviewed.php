<section class="title">
	<h4>Report : Products Most Viewd </h4>
</section>

<section class="item">
	<div class="content">
			<div class='item' id="" style="">

				<?php echo form_open(NC_ADMIN_ROUTE.'/reports/allorders/mostviewed'); ?>
					<input type='hidden' class='' name='reportname' value='mostviewed'>
					Limit:<input type='text' class='' name='limit' value='<?php echo ( isset($limit) )?$limit:10;?>'><br/>
					<input type='hidden' class='' name='include_extra' value='0'>
					<button class="button blue" value="View" name="btnAction" type="submit">View</button>
					<button class="button blue" value="Download" name="btnAction" type="submit">Download</button>
				<?php echo form_close(); ?>		
			</div>
		<br />
		<br />
	<table>
		<thead>
			<tr>
				<th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
				<th>Product ID</th>
				<th></th>								
				<th>Product Name</th>
				<th>Featured</th>
				<th>View</th>				
				<th>Date Created</th>				
			</tr>
		</thead>
		<tbody>

			<?php $data = new StdClass();
				foreach ($reportdata AS $key => $value):  ?>

							<tr class="<?php echo alternator('even', ''); ?>">
								<td><input type="checkbox" name="action_to[]" value="<?php echo $key; ?>"  /></td>
								<td><?php echo $value['id']; ?></td>		
								<td></td>																		
								<td><a class='' href='{{x:uri x='ADMIN'}}/product/edit/<?php echo $value['id']; ?>'><?php echo $value['name']; ?> </a></td>
								<td><?php echo $value['featured']; ?></td>		
								<td><?php echo $value['views']; ?></td>								
								<td><?php echo $value['created']; ?></td>								
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