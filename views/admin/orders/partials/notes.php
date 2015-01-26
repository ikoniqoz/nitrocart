				<fieldset>


	 				<?php echo form_open(NC_ADMIN_ROUTE.'/orders/notes'); ?>
					<?php echo form_hidden('order_id', $order->id); ?>
					<?php echo form_hidden('user_name', ''.$user->username); ?>
					<?php echo form_hidden('user_id', ''.$user->id); ?>
					<ul>
						<li>
							<label>
								<?php /*echo lang('message');*/ ?>
							</label>
							<div class="">
								<?php echo form_textarea(array( 'name' => 'message', 'value' => set_value('message'), 'rows' => 3)); ?>
							</div>
						</li>
						<li>
							<div class="">
								<span class='gt'><?php echo gravatar($user->email);?></span>
								<?php echo form_submit('save', 'Add note', 'class="button"'); ?>
							</div>
						</li>
					</ul>
				<?php echo form_close(); ?>
				</fieldset>
				<div style="overflow-y:scroll;max-height:250px;">
				<fieldset>
						<table>
							<tbody>

							<?php foreach ($notes as $item): ?>
								<?php $_user = $this->db->where('id', $item->user_id )->get('users')->row();  ?>
						   		<tr>
						   			<td>
							   		 	<span class='gt'><?php echo gravatar($_user->email);?></span>
							   		</td>
									<td>
		 								<i><?php echo user_displayname($item->user_id);  ?></i>
		 							</td>
									<td>
		 								<i><?php echo date('Y-m-d H:i:s', $item->date); ?></i>
		 							</td>
		 							<td>
										<?php echo $item->message; ?>
									</td>
							  	</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
				</fieldset>
			</div>