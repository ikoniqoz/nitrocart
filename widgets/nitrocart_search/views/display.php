<?php if($enabled) : ?>

<?php echo form_open('{{x:uri}}/search/'); ?>

		<input type="text" id="nct_search_box" name="nct_search_box" />
		<input type="submit" name="submit" value="Search"  />

<?php echo form_close(); ?>

<?php endif; ?>
