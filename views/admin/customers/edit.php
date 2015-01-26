
<section class="title">
		<h4><?php echo "Customer : " . user_displayname($customer_id, false) . " | UserID: {$customer_id} "; ?></h4>
</section>

<section class="item">
	<div class="content">
				<fieldset>
					<?php echo $user->display_name. ' <h3>' . $user->first_name . ' ' . $user->last_name . '</h3>';?>
				</fieldset>

				<fieldset>
					<?php echo $tabbed_html;?>
				</fieldset>
			
	</div>
</section>