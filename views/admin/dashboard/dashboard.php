	<div class="one_third" id="">
	    <section class="title">
	        <h4>24 hours Revenue</h4>
	    </section>
	    <section class="item">
	        <div class="content">
	        	<h4 class='dash_money'>$ {{revenue_today}}</h4>
	        </div>
	    </section>
	</div>

	<div class="one_third" id="">
	    <section class="title">
	        <h4>30 day Revenue</h4>
	    </section>
	    <section class="item">
	        <div class="content">
				<h4 class='dash_money'>$ {{revenue_monthly}}</h4>
	        </div>
	    </section>
	</div>

	<div class="one_third last" id="">
	    <section class="title">
	        <h4>365 day Revenue</h4>
	    </section>
	    <section class="item">
	        <div class="content">
					<h4 class='dash_money'>$ {{revenue_anual}}</h4>
	        </div>
	    </section>
	</div>		



	<div class="one_full" id="" style=''></div>
	

	<div class="one_half" id="">
	    <section class="title">
	        <h4><?php echo lang('nitrocart:dashboard:orders_7days'); ?></h4>
	    </section>
	    <section class="item">
	        <div class="content">
                <div id="chart_div" style="width: 100%; height: 230px;"></div>
                <div id='chart_legend'></div>
	        </div>
	    </section>
	</div>

	<div class="one_half last" id="">
		<section class="title">
			<h4><?php echo lang('nitrocart:dashboard:overview'); ?></h4>
			<a class="" title=""></a>
		</section>
		<section class="item">
			<div class="content">
				<table>
					<?php foreach($cat as $key => $val): ?>
						<tr>
							<td>
							<?php echo lang('nitrocart:dashboard:'.$key);?>
							</td>
							<td>
								<?php echo $val;?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
	
			</div>
		</section>
	</div>

