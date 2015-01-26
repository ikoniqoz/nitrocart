
	<div class="content">
		<fieldset>
			<ul>
				<li>
					<form action='admin/nitrocart/affiliates/generate/' method='get'>
						<label>Generate a link</label><br/>
						Enter a link here: <br/>
						<label>Display Text for link</label>
						<br/>
						<br/>
						<a href='#' class='btn reload_affiliates'>Reload affiliate <i class='icon-refresh'></i></a>
						<div id="SelectArea">
							
						</div>			
						<div id='was_hidden' style='display:none'>	
							<input id='text' type='text' name='text_to_display' placeholder='Click me'><br/>
							<a class='btn orange getlink'>Generate a link</a>
						</div>
					</form>
					<div id="LinkArea">

					</div>
				</li>

			</ul>
		</fieldset>
	</div>



<script>


        $(document).on('click', '.reload_affiliates', function(event)
        {
            var postto = "admin/nitrocart/affiliates/get_select_pt/";
	        $.post(postto).done(function(data)   
	        {  
	        	$('#SelectArea').html(data);  
	        	$('#was_hidden').show(); 
	        });

             // Prevent Navigation
             event.preventDefault();

        });

        $(document).on('click', '.getlink', function(event)
        {
        	  var _code = $("select[name='client_code']").val();


              var postto = "admin/nitrocart/affiliates/generate_pt/";
              var _url = "<?php echo site_url().NC_ROUTE.'/products/product/'.$id;?>";
              var _text = $('#text').val();
            
              var senddata = {url:_url, text:_text,code:_code };

              $.post(postto,senddata).done(function(data)
              {
              		$('#LinkArea').html(data);
              });

              // Prevent Navigation
              event.preventDefault();

        });
</script>