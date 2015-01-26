			<li><a href="#order-tab"><span><?php echo lang('nitrocart:orders:details'); ?></span></a></li>

			<?php if($order->shipping_address_id == $order->billing_address_id):?>
				<li><a href="#billing-tab"><span>Billing + Shipping Address</span></a></li>
			<?php else:?>
				<li><a href="#billing-tab"><span>Billing Address</span></a></li>
				<?php if($order->shipping_address_id !== NULL):?>
					<li><a href="#delivery-tab"><span>Shipping Address</span></a></li>
				<?php endif;?>
			<?php endif;?>

			
			
			<?php if( $this->config->item('admin/orders/show_items_tab') ) : ?> 
				<li><a href="#contents-tab"><span><?php echo lang('nitrocart:orders:items'); ?></span></a></li>
			<?php endif;?>


            <li><a href="#invoice-tab"><span>Invoice</span></a></li>
			

			<?php if( $this->config->item('admin/orders/show_txn_tab') ) : ?> 
				<li><a href="#transactions-tab"><span><?php echo lang('nitrocart:orders:transactions'); ?></span></a></li>
			<?php endif;?>


			<?php if( $this->config->item('admin/orders/show_notes_tab') ) : ?> 
				<li><a href="#notes-tab"><span><?php echo lang('nitrocart:orders:notes'); ?></span></a></li>
			<?php endif;?>




			<?php if($show_actions): ?>			
			<li><a href="#actions-tab"><span><?php echo lang('nitrocart:orders:actions'); ?></span></a></li>
			<?php endif;?>			