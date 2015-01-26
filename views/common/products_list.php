

    <div class="product-list">

        Order By: 
        <a href='{{x:uri}}/products/orderby/views/asc'>Views ASC</a> | 
        <a href='{{x:uri}}/products/orderby/views/desc'>Views DESC</a> | 
        <a href='{{x:uri}}/products/orderby/name/asc'>Name ASC</a> | 
        <a href='{{x:uri}}/products/orderby/name/desc'>Name DESC</a> | 

        <a href='{{x:uri}}/products/orderby/ordering_count/asc'>Custom Order ASC</a><br/>
        <a href='{{x:uri}}/products/orderby/ordering_count/desc'>Custom Order DESC</a><br/>

        <form action='{{x:uri}}/products/orderby' method='post'>
            <select name='display_list_filter'>
                <option value='views/asc'>Views (Asc)</option>
                <option value='views/desc'>Views (Desc)</option>
                <option value='name/asc'>Name (Asc)</option>                
                <option value='name/desc'>Name (Desc)</option>
            </select>
            <input type='submit' value='Update'>
        </form>

        
        {{if product_count==0}}
            <h3>{{ helper:lang line="nitrocart:messages:product:no_products" }}</h3>
        {{else}}

            {{ products }}

                    {{shop_adina:description id='{{id}}' x='TRIM,DESC_FALLBACK' }}

                    <div>

                            <h4><a itemprop="url" href="{{x:uri}}/products/product/{{ slug }}">{{name}}</a></h4>

                            <a itemprop="url" href="{{x:uri}}/products/product/{{ slug }}">
                                {{nitrocart_gallery:htmlcover product='{{id}}' width='60' }}
                            </a>

                            <form action="{{x:uri}}/cart/add" name="form_{{ id }}" method="post">

                                {{products:htmlvariances product='{{id}}' x='SELECT,PRICE' }}

                                <input type='text' name='qty' placeholder='1'>
                                <input type='submit' value='Add to Cart'>

                            </form>

                    </div>

            {{ /products }}

        {{ endif }}
    </div>


    {{ if pagination:links }}
            {{ pagination:links }}
    {{ endif}}