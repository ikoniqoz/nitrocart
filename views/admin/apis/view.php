
<section class="title">
	<h4>Request for: {{keydata.name}}</h4>
</section>

<section class="item">

	<div class="content">
	
		<table>
				<tr>
					<th>ID</th>
					<th>Endpoint ID</th>
					<th>Date</th>
					<th class='actions'></th>
				</tr>
			
			{{items}}
				<tr>
					<td>{{id}}</td>
					<td>{{endpoint}}</td>
					<td>{{date}}</td>
					<td>
						<span style='float:right'>
						</span>
					</td>
				</tr>
			{{/items}}
		</table>

		{{pagination:links}}
	
	</div>

</section>