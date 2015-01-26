
<h2>Wishlist</h2>


<div id="">
	<div>
            {{ if items }}
		<table>
			<thead>
				<tr>
					<th class="image"></th>
					<th class="description">Item</th>
					<th class="subtotal">Price</th>
					<th></th>
				</tr>
			</thead>
			{{items}}
				<tr>
					<td>

							{{nitrocart_gallery:cover id="{{id}}" include_cover='YES' include_gallery='NO' }}

									{{if local}}
										<img itemprop="image" src="{{ url:site }}files/thumb/{{file_id}}/100/100/" width="100" height="100" alt="{{alt}}" />
									{{else}}
										<img itemprop="image" src="{{src}}" width="100" height="100" alt="{{alt}}" />
									{{endif}}

							{{/nitrocart_gallery:cover}}

					</td>
					<td><a href="{{x:uri}}/products/product/{{slug}}">{{name}}</a></td>
					<td>{{price_or}}</td>
					<td><a class="DeleteButton" href="{{x:uri}}/my/wishlist/delete/{{id}}">Remove</a></td>
				</tr>
			{{/items}}
		</table>
                {{ else }}
                    <h4>Empty List</h4>
                {{ endif }}
		<p>
			<a href="{{x:uri}}/my">Back to Dashboard</a>
		</p>

	</div>
</div>