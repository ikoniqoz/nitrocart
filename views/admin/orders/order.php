
<?php
 $paid_status = ($order->paid_date == NULL)? 'unpaid' : 'paid';
?>

<style>
 	#progressbar {
      background-color: #eee;      
      border-radius: 13px; /* (height of inner div) / 2 + padding */
      padding: 1px;
      width:85%;
      margin-top:15px;
      float:left;
      border:1px solid #ccc;
    }

    #progressbar > div {
       background-color: #FFaa55;
       color:#eee;
       width:<?php echo $percent_value;?>%; /* Adjust with JavaScript */
       height: 20px;
       border-radius: 10px;
   /*var color = $( this ).css( "background-color" );*/
 }
</style>
<style>
ul.tab-menu {
    list-style:none;
}
</style>
<?php //$class_name = 's_'.$order->status.''; ?>

<?php $status_color = Helper_order_status_color($order->status_id); ?>
<?php $paid_color = Helper_order_paid_status_color($paid_status); ?>

<section class="title" style='height:40px;border-radius:0px;'>
    <span style='margin-top:10px;float:left'>
        <h4>
            <em><?php echo lang('nitrocart:orders:account'); ?></em>: <?php echo  $customer->display_name; ?>  --  [ORDER #: <?php echo $order->id; ?> ]</span>
        </h4>
    </span>
    <span style="float:right;margin-right:10px;">	
		<h4 style="text-shadow:none">
			<?php if($order->deleted != NULL): ?>			
			<div class='stags red'>Deleted</div><?php endif; ?>			
			<div class='stags <?php echo $paid_color;?>'><?php echo ucfirst($paid_status); ?></div>
			<div class='stags <?php echo $status_color;?>'><?php echo ($order->current_status) ? $order->current_status->name : 'Not Set'; ?></div>
		</h4>        
    </span>
</section>
<section class="item">

	<div class="content">

<?php if( $this->config->item('admin/orders/show_info_status') ) : ?> 
		<fieldset>
			<div id="order-tab" class="form_inputs">
				<span class='gt' style='float:right'>
					<?php echo gravatar( $invoice->email); ?>
				</span>
			    <div id="progressbar">
			      <div><center><?php echo $percent_value;?>%</center></div>
			    </div>
			</div>
		</fieldset>
<?php endif;?>
		<div class="tabs">
			<ul class="tab-menu">
				<?php $this->load->view('admin/orders/partials/tabs'); ?>
			</ul>
			<div id="order-tab" class="form_inputs">
					<?php $this->load->view('admin/orders/partials/details'); ?>
			</div>


			<!-- always display billing-->
			<div id="billing-tab" class="form_inputs">
				<?php $this->load->view('admin/orders/partials/billing'); ?>
			</div>
			<?php if($order->shipping_address_id != $order->billing_address_id AND $order->shipping_address_id != NULL):?>
				<div id="delivery-tab" class="form_inputs">
					<?php $this->load->view('admin/orders/partials/shipping'); ?>
				</div>
			<?php endif;?>


			<?php if( $this->config->item('admin/orders/show_items_tab') ) : ?> 
				<div id="contents-tab" class="form_inputs">
					<?php $this->load->view('admin/orders/partials/items'); ?>
				</div>
			<?php endif;?>


			<div id="invoice-tab" class="form_inputs">
				<?php $this->load->view('admin/orders/partials/invoice'); ?>
			</div>

			<?php if( $this->config->item('admin/orders/show_txn_tab') ) : ?> 
				<div id="transactions-tab" class="form_inputs">
					<?php $this->load->view('admin/orders/partials/transactions'); ?>
				</div>
			<?php endif;?>


			<?php if( $this->config->item('admin/orders/show_notes_tab') ) : ?> 
				<div id="notes-tab" class="form_inputs">
					<?php $this->load->view('admin/orders/partials/notes'); ?>
				</div>
			<?php endif;?>
			
			<?php if($show_actions): ?>
			<div id="actions-tab" class="form_inputs">
				<?php $this->load->view('admin/orders/partials/actions'); ?>
			</div>
			<?php endif;?>
		</div>
				

		<a href='{{x:uri x='ADMIN'}}/orders' class='btn gray'>All Orders</a>

	</div>


</section>
