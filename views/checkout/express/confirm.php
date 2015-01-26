 <form name='myform' action='{{x:uri}}/checkout/express' method='post'>

    <input type="hidden" name="gateway_id" value="{{gateway_id}}">
    <input type="hidden" name="billing_id" value="{{billing_address.id}}">
    <input type="hidden" name="shipping_id" value="{{shipping_address.id}}">
    <input type="hidden" name="shipment_id" value="{{shipments.id}}">

    <h2>Express Checkout</h2>
    <br/>
    <h4>Billing Address</h4>
                <br/>
                <table>
                    <tr>
                        <td>
                            {{billing_address.email}}
                        </td>
                        <td>
                            {{billing_address.address1}}, {{billing_address.address2}}
                        </td>
                        <td>
                            {{billing_address.city}} {{billing_address.country}}
                        </td>
                        <td>
                            {{billing_address.state}} {{billing_address.zip}}
                        </td>
                        <td>
                            {{billing_address.phone}}
                        </td>
                    </tr>
                 </table>

    <br/>



    <h4>Shipping Address</h4>
    <br/>
                <table>
                    <tr>
                        <td>
                            {{shipping_address.email}}
                        </td>
                        <td>
                            {{shipping_address.address1}}, {{shipping_address.address2}}
                        </td>
                        <td>
                            {{shipping_address.city}} {{shipping_address.country}}
                        </td>
                        <td>
                            {{shipping_address.state}} {{shipping_address.zip}}
                        </td>
                        <td>
                            {{shipping_address.phone}}
                        </td>
                    </tr>
                 </table>


    <br/>
    <br/>
    <h4>Shipping Method</h4>
    {{shipments.title}}<br/>
    Shipping Cost:  {{ship_cost}}<br/>

    <br/> <br/>
    <h4>Payment</h4>
    <table>
        <tr>
            <td colspan='5'>
                ID:{{gateway.id}}
            </td>
        </tr>
        <tr>
            <td colspan='5'>
                name:{{gateway.title}}
            </td>
        </tr>
     </table>
    <br/>

        <input type='submit' name='submit' value='Confirm'>
</form>

<a href='{{x:uri}}/checkout'>No thanks, I want to checkout the traditional way.</a>