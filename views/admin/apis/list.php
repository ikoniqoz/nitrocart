
<section class="title">
	<h4>API Keys</h4>
</section>

<section class="item">


	<div class="content">

		<a class='btn blue' href='{{x:uri x='ADMIN'}}/apis/create'>Create New</a><br/><br/>
	
		<table>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Key</th>
					<th>Total Req</th>
					<th>Curr Req</th>
					<th>Max Allowed</th>
					<th class='actions'><?php echo lang('nitrocart:admin:actions');?></th>
				</tr>
			
			{{keys}}
				<tr>
					<td>{{id}}</td>
					<td>{{name}}</td>
					<td>{{key}}</td>
					<td>{{tot_request}}</td>
					<td>{{tot_curr_request}}</td>
					<td>{{max_allowed}}</td>
					<td>
						<span style='float:right'>
							<a class='button' href='{{x:uri x='ADMIN'}}/apis/edit/{{id}}'>Edit</a>
							<a class='button blue' href='{{x:uri x='ADMIN'}}/apis/view/{{id}}'>View Request</a>							
							<a class='button red delete confirm' href='{{x:uri x='ADMIN'}}/apis/delete/{{id}}'>Delete</a>
						</span>
					</td>
				</tr>
			{{/keys}}
		</table>

		{{pagination:links}}
	
	</div>

</section>