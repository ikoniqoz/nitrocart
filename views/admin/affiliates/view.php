<section class="title">

	<h4><?php echo "View {$type->name}"; ?></h4>

</section>

<section class="item form_inputs">

	<div class="content">
		<fieldset>
			<ul>
				<li class="<?php echo alternator('even', ''); ?>">
					<label for="name"><?php echo lang('nitrocart:common:name');?><span>*</span></label>
					<div class="input">
						<?php echo $type->name;?>
					</div>
				</li>

				<li class="<?php echo alternator('even', ''); ?>">
					<label for="name"><?php echo lang('nitrocart:code');?><span></span></label>
					<div class="input">
						<?php echo $type->code;?>
					</div>
				</li>
				<li class="<?php echo alternator('even', ''); ?>">
					<label for="name">Example Usage<span></span></label>
					<div class="input">
						<span class='stags blue'>{{url:site}}?QUANTAM=<?php echo $type->code;?></span> <br/>
					</div>
				</li>
				<li>
				Be sure to test your links before mailing it to your affiliate or affiliate group.

				</li>
				<li>
					<form action='admin/nitrocart/affiliates/generate/' method='get'>
						<label>Generate a link</label><br/>
						Enter a link here: <br/>
						<input type='text' id='url' name='url' placeholder='{{url:site}}'><br/>
						<label>Display Text for link</label><br/>
						<input id='text' type='text' name='text_to_display' placeholder='Click me'><br/>
						<input type='hidden' name='client_code' value='<?php echo $type->code;?>'>
						<a class='btn orange getlink'>View Link</a>
					</form>
					<div id="LinkArea">

					</div>
				</li>

			</ul>
		</fieldset>

		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array( 'cancel'))); ?>
		</div>

	</div>

</section>

<script>

        $(document).on('click', '.getlink', function(event)
        {
              var postto = "admin/nitrocart/affiliates/generate/";
              var _url = $('#url').val();
              var _text = $('#text').val();
              var _code = '<?php echo $type->code;?>';
              var senddata = {url:_url, text:_text,code:_code };

              $.post(postto,senddata).done(function(data)
              {
              		$('#LinkArea').html(data);
              });

              // Prevent Navigation
              event.preventDefault();

        });
</script>