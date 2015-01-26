			<fieldset>

		<?php if($order->deleted==NULL):?>

				<ul>
					<li>
						<label>Order Status</label>
						<div class='input'>
							You can change the status of this order by selecting a new status and press Confirm
						</div>
						<div>
							<form action="{{x:uri x='ADMIN'}}/orders/setstatus/{{order.id}}/{{order.status_id}}" method="post">
									<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />

								<?php echo form_dropdown('order_status',$order_workflows, $order->status_id);?>

								<br />
								<span class=''>
									<button type="submit" class="button blue confirm" title='Please confirm that you want to change the order status.'><span>Change Status</span></button>
								</span>

							</form>
						</div>
					</li>
			
				<?php if($admin_can_delete):?>
					<li>
						<label>If you delete an order you will not be able to view it again, please take care before deleting. In most cases a cancel or close order is most suitable</label>
						<div class='input'>
							<a class='btn red delete confirm' href='{{x:uri x='ADMIN'}}/orders/delete/<?php echo $order->id;?>'>Delete Order</a>
						</div>
					</li>		
				</ul>
			<?php endif;?>

		<?php if($order->paid_date == NULL):?>
			<a class='btn green confirm' href='{{x:uri x='ADMIN'}}/orders/mapaid/{{order.id}}' title='Are you sure you want to mark this order as PAID ?'>Mark as PAID</a>
					
			<a class='btn orange confirm' href='{{x:uri x='ADMIN'}}/orders/reinvoice/{{order.id}}' title='Are you sure ?'>Send Invoice</a>
		<?php endif;?>

	<?php endif;?>
</fieldset>