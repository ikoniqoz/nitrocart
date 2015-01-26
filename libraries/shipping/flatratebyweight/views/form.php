<ul>
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label>Amount (Per 1 Kilogram)</label>
		<div class="input">
			<?php echo form_input('options[amount]', set_value('options[amount]', $options['amount'])); ?>
		</div>
	</li>
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label>Handling (once off)</label>
		<div class="input">
		<?php $options['handling'] = (isset($options['handling']) )?$options['handling']:0 ; ?>
				<?php echo form_input('options[handling]', set_value('options[handling]',$options['handling']  ) ); ?>
		</div>
	</li>
</ul>