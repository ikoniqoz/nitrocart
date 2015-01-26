
	<fieldset>
				<table>
					<thead>
						<tr>
							<th><?php echo lang('nitrocart:orders:image'); ?></th>
							<th><?php echo lang('nitrocart:orders:item'); ?></th>
							<th>Variation</th>
							<th><?php echo lang('nitrocart:orders:qty'); ?></th>
						</tr>
					</thead>
					<tbody>
							{{contents}}
							<tr>
								<td rowspan="2">
					                {{ nitrocart_gallery:cover product_id="{{id}}" x="" }}
					                        <img src="{{ url:site }}files/thumb/{{file_id}}/120"  alt="{{alt}}" />
					                {{ /nitrocart_gallery:cover }}
								</td>
								<td>
									<a target='new' class='nc_links' href='{{x:uri x='ADMIN'}}/product/view/{{id}}'><i style='color:#f00'  class='icon-external-link'></i> {{name}} </a>
								</td>
								<td>
									<a target='new'class='nc_links' href='{{x:uri x='ADMIN'}}/product/variant/{{variant_id}}'><i style='color:#f00'  class='icon-external-link'></i> {{products:variant id="{{variant_id}}" x="NAME" }} </a>							
								</td>
								<td>{{qty}}</td>
							</tr>
							<tr>
								<td colspan="8">{{options}}</td>
							</tr>
						{{/contents}}
					</tbody>
				</table>
			</fieldset>