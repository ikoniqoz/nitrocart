 <?php (isset($options['amount']))?:$options['amount']=0;?>
 <?php (isset($options['handling']))?:$options['handling']=0;?>
 <?php (isset($options['usepackages']))?:$options['usepackages']=1;?>
<ul>
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label>Amount (Per item)</label>
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
    <li class="">
        <label>Packaging System</label>
        <div class="input">
            <?php echo form_dropdown('options[usepackages]', array('packages'=>'Packaging Subsystem', 'items'=>'Do not use Packages') ,  (isset($options['usepackages'])? $options['usepackages'] :0 )  ); ?>
        </div>
    </li>	
</ul>