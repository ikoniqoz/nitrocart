<?php if(!(isset($options['apikey'])) ): ?>
    <?php $options['apikey']='';?>
<?php endif; ?>
<?php if(!(isset($options['keepresponses'])) ): ?>
    <?php $options['keepresponses']='';?>
<?php endif; ?>
<?php if(!(isset($options['packagetype'])) ): ?>
    <?php $options['packagetype']='Parcel';?>
<?php endif; ?>
<?php if(!(isset($options['distcode'])) ): ?>
    <?php $options['distcode']='2000';?>
<?php endif; ?>
<?php if(!(isset($options['mincharge'])) ): ?>
    <?php $options['mincharge']=0;?>
<?php endif; ?>
<?php if(!(isset($options['maxcharge'])) ): ?>
    <?php $options['maxcharge']=0;?>
<?php endif; ?>
<?php if(!(isset($options['handling'])) ): ?>
    <?php $options['handling']=0;?>
<?php endif; ?>   
<?php if(!(isset($options['usepackages'])) ): ?>
    <?php $options['usepackages']=0;?>
<?php endif; ?>   
<?php if(!(isset($options['RFCode'])) ): ?>
    <?php $options['RFCode']=0;?>
<?php endif; ?>   
<?php if(!(isset($options['multiregion'])) ): ?>
    <?php $options['multiregion']='false';?>
<?php endif; ?>   


        <div class="tabs">
                    <!-- Here we create the tabs -->
                    <ul class="tab-menu">
                        <li><a href="#tab-1"><span>Access</span></a></li>
                        <?php if(isset($options['apikey'])):?>
                            <?php if(trim($options['apikey']) != ""):?>
                                <li><a href="#tab-2"><span>Distribution</span></a></li>
                                <li><a href="#tab-3"><span>Adjustments</span></a></li>
                            <?php endif;?>
                        <?php endif;?>
                    </ul>

                    <div class="form_inputs" id="tab-1">
                        <section class="item">
                            <div class="content">
                                 <ul>
                                    <li>
                                        <label>API Key</label>
                                        <div class="input">
                                            <?php echo form_input('options[apikey]', set_value('options[apikey]', $options['apikey'])); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <label>Keep Curl Request Responses, Only turn this on while testing.</label>
                                        <div class="input">
                                            <?php echo form_dropdown('options[keepresponses]', $form_data['KEEP_RESPONSES'] , $options['keepresponses']); ?>
                                        </div>
                                    </li>
                                    <li>
                                        <label>Packaging System</label>
                                        <div class="input">
                                            <?php echo form_dropdown('options[usepackages]', array('packages'=>'Packaging Subsystem', 'items'=>'Do not use Packages') , set_value('options[usepackages]', $options['usepackages'])  ); ?>
                                        </div>
                                    </li>
                                 </ul>
                            </div>
                        </section>     

                <?php if(isset($options['apikey'])):?>
                    <?php if(trim($options['apikey']) != ""):?>

                    </div>
                    <div class="form_inputs" id="tab-2">
                        <section class="item">
                            <div class="content">

                                <ul>
                                    <li class="">
                                        <label>Distribution PostCode</label>
                                        <div class="input">
                                            <?php echo form_input('options[distcode]', set_value('options[distcode]', $options['distcode'])); ?>
                                        </div>
                                    </li>                                    
                                    <li class="">
                                        <label>RFCode Code</label>
                                        <div class="input">
                                            <?php echo form_dropdown('options[RFCode]', $form_data['RFCODES'] , (isset($options['RFCode'])?$options['RFCode']:'SYD')); ?>
                                        </div>
                                    </li>
                                    <li class="">
                                        <label>Package Type</label>
                                        <div class="input">
                                            <?php echo form_dropdown('options[packagetype]', $form_data['PACKAGE_TYPES'] , $options['packagetype']); ?>
                                        </div>
                                    </li>
                                    <li class="">
                                        <label>Package Type</label>
                                        <div class="input">
                                            <?php echo form_dropdown('options[multiregion]', $form_data['MULTI_REGIONS'] , $options['multiregion']); ?>
                                        </div>
                                    </li>


                                </ul>
                           </div>
                        </section>                          
                    </div>
                    <div class="form_inputs" id="tab-3">
                        <section class="item">
                            <div class="content">

                                <div>
                                    The values listed here ONLY take affect once they are larger than ZERO.
                                </div>

                                <ul>                                 
                                    <li class="">
                                        <label>Min Charge</label>
                                        <div class="input">
                                            <?php echo form_input('options[mincharge]', set_value('options[mincharge]', $options['mincharge'])); ?>
                                        </div>
                                    </li>

                                    <li class="">
                                        <label>MAX Charge</label>
                                        <div class="input">
                                            <?php echo form_input('options[maxcharge]', set_value('options[maxcharge]', $options['maxcharge'])); ?>
                                        </div>
                                    </li>



                                    <li class="">
                                        <label>Handling Fee</label>
                                        <div class="input">
                                            <?php echo form_input('options[handling]', set_value('options[handling]', $options['handling'])); ?>
                                        </div>
                                    </li> 
                                </ul>

                            </div>
                        </section>     

                    </div> 


                     <?php endif;?>
                <?php endif;?>          
        </div>
