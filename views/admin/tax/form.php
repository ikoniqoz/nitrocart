<div class="one_half" id="">
	<section class="title" style='height:40px;'>
	    <span id='nc_logo' style=''></span>
	    <span style="padding:10px;float:left;">
			<h4><?php echo lang('nitrocart:tax:title');?></h4>
		</span>
	</section>

	<section class="item">
		<div class="content">
			<table>
				<thead>
					<tr>
						<th><input type="checkbox" name="action_to_all" value="" class="check-all" /></th>
						<th><?php echo lang('nitrocart:admin:id');?></th>
						<th><?php echo lang('nitrocart:admin:name');?></th>
						<th><?php echo lang('nitrocart:tax:rate');?></th>
						<th>Is Default</th>
						<th style="width: 120px"></th>
					</tr>
				</thead>
				<tbody>
						<tr>
							<td></td>
							<td></td>
							<td><?php echo lang('nitrocart:tax:none');?></td>
							<td>0 %</td>
							<td>NULL</td>
							<td>
							</td>
						</tr>

					<?php foreach ($all_taxes AS $tax): ?>
						<tr>
							<td><input type="checkbox" name="action_to[]" value="<?php echo $tax->id; ?>"  /></td>
							<td><?php echo $tax->id; ?></td>
							<td><?php echo $tax->name; ?></td>
							<td><?php echo $tax->rate; ?> %</td>
							<td><?php echo yesNoBOOL($tax->default, 'string', '<span style="color:green">Yes</span>','<span style="color:gray">No</span>'); ?></td>
							<td>

							<?php
								$show = true;
								if(isset($id))
								{
									if($id==$tax->id)
									{
										$show=false;
									}
								}

								if($show)
								{
									$items = array();

									$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/tax/edit/{$tax->id}", false,  lang('nitrocart:admin:edit'), false, "eye-open");
									$items[] = dropdownMenuStandard(NC_ADMIN_ROUTE."/tax/delete/{$tax->id}", true, lang('nitrocart:admin:delete'), true, "minus");

									echo dropdownMenuList($items,'');
								}

								?>

							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
		</div>
	</section>
</div>


<div class="one_half last" id="">
	<section class="title" style='height:40px;'>
	    <span id='nc_logo' style=''></span>
	    <span style="padding:10px;float:left;">
			<h4><?php echo (isset($id) && $id >0)?'Edit':'Create';?></h4>
		</span>
	</section>

	<?php echo form_open_multipart($this->uri->uri_string()); ?>
	<?php echo form_hidden('id', set_value('id', $id)); ?>
	<section class="item">
		<div class="content">


					<fieldset>
						<ul>
							<li class="">
								<label for="name"><?php echo lang('nitrocart:admin:name');?> <span>*</span></label>
								<div class="input"><?php echo form_input('name', set_value('name', $name) ); ?> </div>
							</li>
							<li class="">
								<label for="discount_pcent"><?php echo lang('nitrocart:tax:rate');?> <span></span></label>
								<div class="input"><?php echo form_input('rate', set_value('discount_pcent', $rate)); ?> %</div>
								<p>
									The tax rate should be in whole number format (integer)
									For 10% enter the value of `10`. For half a percent type `0.5`
								</p>
							</li>
							<li class="">
								<label for="default">Is Default<span></span></label>
								<div class="input"><?php echo form_dropdown('default', array(0=>'No',1=>'Yes'),  $default ); ?> </div>
							</li>							
						</ul>
						<div class="buttons">
							<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
						</div>
					</fieldset>

		</div>
	</section>
</div>
<?php form_close(); ?>
