
<?php if(!(isset($options['apikey'])) ): ?>
    <?php $options['apikey']='';?>
<?php endif; ?>

<?php if(!(isset($options['distcode'])) ): ?>
    <?php $options['distcode']='2000';?>
<?php endif; ?>
<?php if(!(isset($options['extracover'])) ): ?>
    <?php $options['extracover']=0;?>
<?php endif; ?>
<?php if(!(isset($options['deliveryoption'])) ): ?>
    <?php $options['deliveryoption']='express';?>
<?php endif; ?> 
<?php if(!(isset($options['usepackages'])) ): ?>
    <?php $options['usepackages']=0;?>
<?php endif; ?>   

<ul>
    <li class="">
        <label>API Key</label>
        <div class="input">
            <?php echo form_input('options[apikey]', set_value('options[apikey]', $options['apikey'])); ?>
        </div>
    </li>
    <li class="">
        <label>Distribution PostCode</label>
        <div class="input">
            <?php echo form_input('options[distcode]', set_value('options[distcode]', $options['distcode'])); ?>
        </div>
    </li>

    <li class="">
        <label>Extra Cover (ZERO if none)</label>
        <div class="input">
            <?php echo form_input('options[extracover]', set_value('options[extracover]', $options['extracover'])); ?>
        </div>
    </li>
    <li class="">
        <label>Service Option</label>
        <p>
            Choose From Regular or Express Shipping
        </p>
        <div class="input">
            <?php echo form_dropdown('options[deliveryoption]', array('express'=>'ExpressPost', 'regular'=>'Regular Post') , set_value('options[deliveryoption]', $options['deliveryoption'])  ); ?>
        </div>
    </li>
    <li class="">
        <label>Packaging System</label>
        <p>
            <ul>
                <li><a target='new' href='https://github.com/dvdoug/BoxPacker'>NitroCart Uses this BoxPacker library</a></li>
                <!--li><a target='new' href='http://www.3dbinpacking.com/'>http://www.3dbinpacking.com/</a></li-->
                <li><a target='new' href='http://en.wikipedia.org/wiki/Bin_packing_problem'>Wikipedia Article on Bin Packing</a></li>
            </ul>
        </p>
        <div class="input">
            <?php echo form_dropdown('options[usepackages]', array('packages'=>'Packaging Subsystem', 'items'=>'Do not use Packages') , set_value('options[usepackages]', $options['usepackages'])  ); ?>
        </div>
    </li>
</ul>

