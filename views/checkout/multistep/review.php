<h2>Review your order</h2>

    <h3>Shopping cart</h3>
    <table>
        <thead>
            <tr>
                <td>Details</td>
                <td>Qty</td>
                <td style="text-align:right">Subtotal</td>
            </tr>
        </thead>
        <tbody>
            {{cart}}
                <tr>
                    <td>{{name}}</td>
                    <td>{{qty}}</td>
                    <td>{{subtotal}}</td>
                </tr>
            {{/cart}} 
                <tr>
                    <td>Shipping</td>
                    <td></td>
                    <td>{{shipping_cost}}</td>
                </tr>   

                <tr>
                    <td>Total</td>
                    <td></td>
                    <td> {{order_total}}</td>
                </tr>   
                         
        </tbody>
    </table>

    <br/><br />

    <h2>Shipping Information</h2>
    <table>
        <tbody>
                <tr>
                    <td> 
                        {{shipping_address.address1}}, {{shipping_address.address2}} {{shipping_address.city}}
                        {{shipping_address.country}}, {{shipping_address.state}}  {{shipping_address.zip}}
                    </td>
                </tr>                
        </tbody>
    </table>


    <form method='post' action='{{x:uri}}/checkout/review'>
        <fieldset>
            <div class="buttons"> 
                <a class="shopbutton" href='{{x:uri}}/cart'>{{ helper:lang line="nitrocart:messages:checkout:back_to_cart" }}</a> or <input class="shopbutton"type='submit' name='submit' value='continue'>
            </div>
        </fieldset>
    </form>

