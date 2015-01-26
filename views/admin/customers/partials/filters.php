<fieldset id="filters">
	<?php echo form_open(NC_ADMIN_ROUTE.'/customers/filter') ?>
	<?php echo form_hidden('f_module', $module_details['slug']) ?>
		<ul>
			<li>
				<label>Name</label><br/>
				<?php echo form_input('f_name',$str_search) ?>
			</li>
			<li>
				<button>Search</button>
				<a href='{{x:uri x='ADMIN'}}/customers/filter/clear'>Clear</a>
			</li>			
		</ul>		
	<?php echo form_close() ?>
</fieldset>