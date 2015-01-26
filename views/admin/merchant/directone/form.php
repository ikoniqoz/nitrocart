<?php if(!(isset($options['test_mode'])) ): ?>
    <?php $options['test_mode']=1;?>
<?php endif; ?>   
<?php if(!(isset($options['companyname'])) ): ?>
    <?php $options['companyname']='';?>
<?php endif; ?>   
<?php if(!(isset($options['vendorname'])) ): ?>
    <?php $options['vendorname']='';?>
<?php endif; ?>   
<?php if(!(isset($options['vendoradminemail'])) ): ?>
    <?php $options['vendoradminemail']='';?>
<?php endif; ?>   
<?php if(!(isset($options['returnlinktext'])) ): ?>
    <?php $options['returnlinktext']='';?>
<?php endif; ?>   
<?php if(!(isset($options['vendorpin'])) ): ?>
    <?php $options['vendorpin']='';?>
<?php endif; ?>   

<ul>
	<li class="">
		<label for="test_mode">Test Mode</label>
		<div class="input">
			<?php echo form_dropdown('options[test_mode]', array(1 => 'Yes', 0 => 'No'), set_value('options[test_mode]', $options['test_mode'])); ?>
		</div>
	</li>
	
	<li class="<?php echo alternator('even', 'odd') ?>">
		<label for="companyname">Company name</label><br/>

		<i>The name assigned to you by DirectOne.</i>
		<div class="input">
			<?php echo form_input('options[companyname]', set_value('options[companyname]', $options['companyname'])); ?>
		</div>
	</li>
	<li class="">
		<label for="vendorname">Vendor Name</label><br/>
		<i>The Vendor name assigned to you by DirectOne</i>
		<div class="input">
			<?php echo form_input('options[vendorname]', set_value('options[vendorname]', $options['vendorname'])); ?>
		</div>
	</li>
	<li class="">
		<label for="vendorpin">Vendor PIN</label><br/>
		<i>Your DirectOne PIN number</i>
		<div class="input">
			<?php echo form_input('options[vendorpin]', set_value('options[vendorpin]', $options['vendorpin'])); ?>
		</div>
	</li>

	<li class="">
		<label for="vendoradminemail">Vendor Email address</label><br/>
		<i>This is the email you used to sign up with DirectOne, any issues contact DirectOne.</i>
		<div class="input">
			<?php echo form_input('options[vendoradminemail]', set_value('options[vendoradminemail]', $options['vendoradminemail'])); ?>
		</div>
	</li>


	<li class="<?php echo alternator('even', 'odd') ?>">
		<label for="returnlinktext">Return Link Text </label><br/>
		<i>example: Click here to return to my site..</i>
		<div class="input">
			<?php echo form_input('options[returnlinktext]', set_value('options[returnlinktext]', $options['returnlinktext'])); ?>
		</div>
	</li>
	
</ul>