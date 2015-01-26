<fieldset>
<ul>
<?php
//echo $tabbeb;

	foreach($extra_fields as $field)
	{
			echo "<li class='input'>";
				echo "<h3>";
					echo $field['name'];
				echo "</h3>";
				echo "<div class='input'>";
					echo $field['form'];
					echo "</div>";
			echo "</li>";
	}

?>
	<li>
		<h3>Tax Class<span></span>
			<span>
				<small>
				</small>
			</span>
		</h3>
		<div class="input">
			<?php echo form_dropdown('tax_id', $tax_select , set_value('tax_id', $tax_id)); ?>
		</div>
	</li>
</ul>
</fieldset>