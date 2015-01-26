
<h4>Product Name</h4>
{{product.name}}




<h4>Adina Description:</h4>
{{shop_adina:description id='{{product.id}}' x='TRIM,DESC_FALLBACK' }}





<h4>Cover image:</h4>
{{nitrocart_gallery:htmlcover product='{{product.id}}' x='PATH' }}
{{nitrocart_gallery:htmlcover product='{{product.id}}' x='SRC' }}
{{nitrocart_gallery:htmlcover product='{{product.id}}' x='THUMB' }}




<h4>Variances and Options:</h4>
<form method="POST" action="{{x:uri}}/cart/add/"  enctype="multipart/form-data" >
    {{products:htmlvariances product="{{product.id}}" x="RADIO,PRICE" }}
    {{shop_options:htmlproduct id="{{product.id}}" }}
    <input type='text' name='qty' placeholder='1'>
    <input type='submit' value='Add to Cart'>
</form>



<h4>List of ALL categories for this product</h4>
<br />
<table border='1' cellpadding=5 style='border-color:#999'>
    <tr>
        <td>Id</td>
        <td>Name</td>
        <td>Slug</td>
    </tr>
    {{nitrocart_categories:product id="{{product.id}}" }}
            <tr class=''>
                <td>{{category_id}}</td>
                <td>{{category_name}}</td>
                <td>{{category_slug}}</td>
            </tr>
    {{/nitrocart_categories:product}}
</table>