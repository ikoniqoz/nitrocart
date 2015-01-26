<table>
	<tr><td width='20%'>Status:</td><td class=''>	<?php echo $status;?>		</td></tr>
	<tr><td>Message:</td><td class=''>				<?php echo $message;?>		</td></tr>
	<tr><td>User:</td><td class=''>					<?php echo $user;?>			</td></tr>
	<tr><td>SYS TXN ID:</td><td class=''>			<?php echo $id;?>			</td></tr>
	<tr><td>Order ID:</td><td class=''>				<?php echo $order_id;?>		</td></tr>
	<tr><td>GTW TXN ID:</td><td class=''>			<?php echo $txn_id;?>		</td></tr>
	<tr><td>GTW TXN Status:</td><td class=''>		<?php echo $txn_status;?>	</td></tr>
	<tr><td>Amount:</td><td class=''>				<?php echo $amount;?>		</td></tr>
	<tr><td>Refunded:</td><td class=''>				<?php echo $refund;?>		</td></tr>
	<tr><td>TimeStamp:</td><td class=''>			<?php echo $timestamp;?>	</td></tr>

	<?php foreach($data as $i => $val) : ?>
			<tr><td><?php echo $i;?></td><td class=''><?php echo $val;?></td></tr>
	<?php endforeach;?>
</table>