{{# #}}
{{# Here you have access to everything in the cart - using the cart variables #}}
{{# #}}
{{ if total }}
    <ul class="cart-widget-items">
        {{ contents }} 
            <li>
                <div class="cart-widget-item-name">{{name}}</div>
                <div class="cart-widget-item-price-qty">ea: ${{price}}&nbsp; | &nbsp;qty: {{ qty }}</div>
            </li>
        {{ /contents }} 
        <li> Total: ${{ total }}</li>
        <li> Total Items: {{ items_count }}</li>       
    </ul>
    <ul class="cart-links">
        <li><a class="shopbutton" href="{{x:uri}}/cart">Update Cart</a></li>
        <li><a class="shopbutton" href="{{x:uri}}/checkout">Checkout</a></li>
    </ul>
{{ else }}
    <h4><?php echo lang('nitrocart:cart:cart_empty'); ?></h4>
{{ endif }}