<div id="CartView">
    <div id="cart-title">
        <h2>Your cart</h2>
    </div>


    {{ nitrocart:cart }}

        {{if item_count == 0}}
                Your cart is empty!
        {{ else }}

            <form action="{{x:uri}}/cart/update" method="POST">
                <!-- Start Shopping Cart Table -->
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th class="image">item</th>
                            <th class="description">Description</th>
                            <th class="price">Price</th>
                            <th class="qty">Quantity</th>
                            <th class="subtotal">Subtotal</th>
                            <th class="remove">Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{contents}}
                		<tr>
                		    <input type="hidden" name="{{rowid}}[rowid]" value="{{rowid}}">
                            <input type="hidden" name="{{rowid}}[id]" value="{{id}}">
                            <input type="hidden" name="{{rowid}}[variance]" value="{{variance}}">


                        	    <td class="image">
                                    <a href="{{x:uri}}/products/product/{{productid}}">
                                        {{ nitrocart_gallery:cover product_id="{{productid}}" x="" }}
                                                <img src="{{url:site}}{{src}}"  alt="{{alt}}" />
                                        {{ /nitrocart_gallery:cover }}
                                    </a>
                	            </td>
                                    <td class="description">{{name}}</td>
                                    <td class="price">{{price}}</td>
                                    <td class="qty"><input type="text" name="{{rowid}}[qty]" value="{{qty}}" maxlength="4"></td>
                                    <td class="subtotal">{{subtotal}}</td>
                                    <td class="remove"><a class="" href="{{x:uri}}/cart/delete/{{rowid}}">&times;</a></td>
                		</tr>
                		{{/contents}}
                        <tr class="cart-actions">
                            <td colspan="6">

                                    Sub total: {{nitrocart:currency}} {{nitrocart:total cart="sub-total"}}

                                    &nbsp;

                                    <input class=""  name="update_cart" type="submit" value="update cart" />

                                    <a class="" href="{{x:uri}}/checkout/">checkout</a>

                                    &nbsp;

                                    <a class="" href="{{x:uri}}/cart/clear/">Clear cart</a>

                            </td>
        		        </tr>
                    </tbody>
        	</table>
            </form>


        {{ endif }}

    {{ /nitrocart:cart }}

    {{nitrocart:expresscheckout text='You are eligable for Express Checkout' class='btn'}}

</div>