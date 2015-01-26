<?php

?>

<button class="btn blue" value="save_exit" name="btnAction" type="submit">
	<span><?php echo lang('nitrocart:products:save_and_exit');?></span>
</button>

<button class="btn blue" value="save" name="btnAction" type="submit">
	<span><?php echo lang('nitrocart:products:save');?></span>
</button>

<span style="color:#ddd">
&nbsp;
</span>

<a href='{{x:uri x='ADMIN'}}/products' class='btn gray'><?php echo lang('nitrocart:products:cancel');?></a>

<span style="color:#ddd">
 |||
</span>

<a class="btn red confirm" href="{{x:uri x='ADMIN'}}/product/delete/{{id}}"><?php echo lang('nitrocart:products:delete');?></a> 