<?php if(!(isset($options['amount'])) ): ?>
    <?php $options['amount']=0;?>
<?php endif; ?>
<?php if(!(isset($options['tax_rate'])) ): ?>
    <?php $options['tax_rate']=0.1;?>
<?php endif; ?>

<ul>
	<li class="">
		<label>Amount</label>
		<div class="input">
			<?php echo form_input('options[amount]', set_value('options[amount]', $options['amount'])); ?>
		</div>
	</li>
	<li class="">
		<label>Tax Rate for this Shipping method ?</label>
		<div class="input">
			<?php 

				$values= [];
				for($i=0;$i<101;$i++)
				{
					if($i<10)
					{
						$values['0.0'.$i] = $i . ' %';
					}
					else if(($i>10) AND ($i<100))
					{
						$values['0.'.$i] = $i . ' %';
					}	
					else if($i == 100)
					{
						$values['1.0'] = $i . ' %';
					}
		
				}

			echo form_dropdown('options[tax_rate]', $values , $options['tax_rate']); ?>
		</div>
	</li>	
</ul>