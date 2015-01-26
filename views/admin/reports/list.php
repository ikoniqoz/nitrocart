<section class="title">
	<h4><?php echo lang('nitrocart:reports:title');?></h4>
	<h4 style="float:right">
	</h4>
</section>

<section class="item">
	<div class="content">

		<table>
			<thead>
				<tr>
					<th></th>
					<th><?php echo lang('nitrocart:reports:type');?></th>				
					<th><?php echo lang('nitrocart:reports:name');?></th>
					<th><?php echo lang('nitrocart:reports:description');?></th>
					<th style="width: 120px"></th>
				</tr>
			</thead>
			<tbody>

					{{reports}}
						<tr class="<?php echo alternator('even', ''); ?>">
							<td></td>
							<td>{{type}}</td>								
							<td>{{name}}</td>
							<td>{{description}}</td>
							<td>
								<span style="float:right;">
										<a class="btn green" href="{{path}}">View</a>
								</span>
							</td>
						</tr>
					{{/reports}}
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8"><div style="float:right;"></div></td>
				</tr>
			</tfoot>
		</table>


	</div>
</section>

{{pagination:links}}
