<div id="sortable">

	<div class="one_full" id="">

		<section class="title">
			<h4>{{title}}</h4>
			<a class="" title=""></a>
		</section>
		<section class="item">

			<div class="content">

				<table>

					Found {{count}} records: <br />

			        <?php foreach($info as $product):?> 
					<tr>
						<td>
							<?php echo $product->id;?> - <i> <?php echo $product->name;?> </i>
						</td>
						<td class='input'>						
							<a target='new' class='btn green' href='{{x:uri x='ADMIN'}}/product/edit/<?php echo $product->id;?>'>Go to Product</a>	
						</td>
					</tr>	
					<?php endforeach;?>
					

				</table>
				<br /><p />
				<a class='btn gray' href='{{x:uri x='ADMIN'}}/maintenance'>&larr; Maintenance</a>
				<a class='btn gray' href='{{x:uri x='ADMIN'}}/maintenance/check_product_type_assoc'>Refresh</a>
			</div>


		</section>

	</div>

</div>
