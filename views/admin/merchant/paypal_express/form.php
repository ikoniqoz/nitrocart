
<ul>
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label for="test_mode"><?php echo lang('nitrocart:gateways:test_mode');?></label>
		<div class="input">
			<?php echo form_dropdown('options[test_mode]', array(true => 'Yes', false => 'No'), set_value('options[test_mode]', $options['test_mode'])); ?>
		</div>
	</li>
	<li class="">
		<label for="test_mode">Skip Confirmation Page</label>
		<div class="input">
			<?php echo form_dropdown('options[auto]', array(1 => 'Yes', 0 => 'No'), set_value('options[auto]', $options['auto'])); ?>
		</div>
	</li>	
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label for="username"><?php echo lang('nitrocart:gateways:username');?></label>
		<div class="input">
			<?php echo form_input('options[username]', set_value('options[username]', $options['username'])); ?>
		</div>
	</li>
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label for="password"><?php echo lang('nitrocart:gateways:password');?></label>
		<div class="input">
			<?php echo form_input('options[password]', set_value('options[password]', $options['password'])); ?>
		</div>
	</li>
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label for="signature"><?php echo lang('nitrocart:gateways:api_signature');?></label>
		<div class="input">
			<?php echo form_input('options[signature]', set_value('options[signature]', $options['signature'])); ?>
		</div>
	</li>
</ul>