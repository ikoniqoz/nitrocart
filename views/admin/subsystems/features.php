<section class="title">
	<h4><?php echo lang('nitrocart:features:title');?></h4>
	<h4 style="float:right">
	</h4>
</section>

<section class="item">
	<div class="content">
		<fieldset id="">
			<div class="item one_full">
					<?php echo lang('nitrocart:features:description');?>
			</div>
		</fieldset>		
		<table>
			<thead>
				<tr>
					<th></th>
					<th><?php echo lang('nitrocart:features:system');?></th>
					<th><?php echo lang('nitrocart:features:version');?></th>
					<th><?php echo lang('nitrocart:features:requires');?></th>
					<th><?php echo lang('nitrocart:features:status');?></th>
					<th><span style="float:right;"><?php echo lang('nitrocart:features:action');?></span></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($subsystems as $key => $value) : ?>
						<tr class="">
							<td></td>
							<td>
								<?php echo $value->title; ?>
								<br/><br/>
								<small>
								<i style='color:#777'>
									<?php echo $value->description; ?>
								</i>
								</small>
							</td>
							<td><?php //echo $value->version; ?></td>
							<td><span style='color:#777'><?php echo sih_slug2title($value->require); ?></span></td>
							<td>
									<?php echo yesNoBOOL($value->installed, 'string', '<span style="color:#aaf">Installed</span>', '' ) ; ?>
									<?php if($value->installed==1): ?>	
									<?php endif;?>	
																			
							</td>
							<td>
								<span style="float:right;">

									<?php if($value->installed==0): ?>
											<a class='button edit_button blue' href='{{x:uri x='ADMIN'}}/features/add/<?php echo $value->driver; ?>'>Add</a>
									<?php else:?>
											<a class='button alert_button red confirm' href='{{x:uri x='ADMIN'}}/features/remove/<?php echo $value->driver; ?>'>Remove</a>
									<?php endif;?>

								</span>
							</td>
						</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<div class="buttons">
		</div>
	</div>

<?php if (isset($pagination)): ?>
	<?php echo $pagination; ?>
<?php endif; ?>