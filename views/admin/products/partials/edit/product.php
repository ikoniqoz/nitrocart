<fieldset>
	{{product_form}}
</fieldset>

<script type="text/javascript">
	(function($) {

		$('input[name="name"]').live('change', function(e)
		{
			var new_name = $(this).val();
			$("#title_product_name").html(new_name);
			return false;
		});

	})(jQuery);
</script>

