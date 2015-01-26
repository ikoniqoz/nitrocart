 


					<?php foreach ($products as $product) : ?>
						<tr style='height:20px;'>
							
							<td>	
								<?php echo form_checkbox('action_to[]', $product->id); ?>
							</td>
							<td>	
								<?php echo $product->id; ?>
							</td>							
							<td>			
								<?php  echo $product->cover_image ;?>	
							</td>		
							<td>
								<?php echo $product->_title_data;?>
							</td>
							<?php 
							if ($this->config->item('admin/show_products_views_field'))
							{
								echo "<td class='collapse'>".(($product->views)?$product->views:0)."</td>";
							}
							?>								
							<?php 
							if ($this->config->item('admin/show_products_featured_field'))
							{
								echo "<td class='collapse'>".$product->_featured_data."</td>"; 
							}
							?>
							<td class="collapse">
								<?php echo $product->_searchable_data;?>
							</td>
							<td class="collapse">
								<?php echo $product->_public_data;?>
							</td>	
							<td class="collapse">
								<?php echo $product->_deleted_data;?>								
							</td>									
							<td>
								<span style="float:right;">
		
										<a class='btn blue' href='{{x:uri x='ADMIN'}}/product/edit/<?=$product->id;?>'>Edit</a>

									<?php if ($product->deleted == NULL):?> 
								 		<?php $this->load->view('nitrocart/admin/fragments/products_list_dropdown', array('id' => $product->id) ); ?>
									<?php else:?>		
										<?php $this->load->view('nitrocart/admin/fragments/products_list_dropdown_deleted', array('id' => $product->id) ); ?>
									<?php endif;?>	
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<td colspan='5'>
							<div class="inner" style="float:left;">
								<button class="btn red" value="multi_edit_option" name="btnAction" type="submit" style="vertical-align:top;">Delete</button>
							</div>					
						</td>
						<td colspan='5'>			
							<div class="inner" style="float:right;">
									<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
							</div>		
						</td>						
					</tr>
					<script>
					tooltip_reset();
					</script>