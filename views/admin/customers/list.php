<section class="title">
	<h4><?php echo lang('nitrocart:customers:active_customers');?></h4>
</section>
<section class="item">
	<div class="content">
		

		<?php $this->load->view('admin/customers/partials/filters'); ?>

		<table>
				<tr>
					<th><?php echo lang('nitrocart:admin:id');?></th>
					<th><?php echo lang('nitrocart:customers:user_id');?></th>
					<th><?php echo lang('nitrocart:admin:name');?></th>
					<th><?php echo lang('nitrocart:admin:email');?></th>
					<th></th>
					<th class='actions'><?php echo lang('nitrocart:admin:actions');?></th>
				</tr>
			
			<?php foreach($customers as $cust):?>
				<tr>
					<td><?php echo $cust->id;?></td>
					<td><?php echo $cust->user_id;?></td>
					<td><?php echo $cust->first_name;?></td>
					<td><?php echo $cust->last_name;?></td>
					<td><?php echo $cust->signup_email;?></td>
					<td>
						<span style='float:right'>

							<a href='{{x:uri x='ADMIN'}}/customers/edit/<?php echo $cust->user_id;?>' class='button'><?php echo lang('nitrocart:admin:edit');?></a>

							<a href='{{x:uri x='ADMIN'}}/customers/orders/<?php echo $cust->user_id;?>' class='button green'><?php echo lang('nitrocart:admin:orders');?></a>
							<a href='{{x:uri x='ADMIN'}}/customers/addresses/<?php echo $cust->user_id;?>' class='button green'>Addresses</a>
							<?php if($admin_id != $cust->user_id): ?>
								<a href='{{x:uri x='ADMIN'}}/customers/group/<?php echo $cust->user_id;?>' class='button red'>Change Membership Group</a>
							<?php endif;?>
							<?php if(group_has_role('users','admin_profile_fields')): ?>
								<a href='admin/users/edit/<?php echo $cust->user_id;?>' class='button red'><?php echo lang('nitrocart:customers:edit_profile');?></a>
							<?php endif;?>
						</span>
					</td>
				</tr>
			<?php endforeach;?>
		</table>


		{{pagination:links}}
	
	</div>
</section>