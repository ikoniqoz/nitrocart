<h2 id="page_title">Select Shipping options</h2>

<form action="{{x:uri}}/checkout/shipment" method="POST" name="">

    <fieldset>
        <table>
        {{shipments}}
            <!--img src="{{image}}"-->
           <tr>
                <td><input type="radio" value="{{id}}" name="shipment_id" checked></td>
                <td>{{title}}</td>
                <td>{{nitrocart:currency}} {{shipping_cost}}</td>
            </tr>
           <tr>
                <td colspan='3'>{{desc}}</td>
            </tr>            
        {{/shipments}}
        </table>
    </fieldset>

    <fieldset>    
        <div class="buttons"> 
            <a href='{{x:uri}}/cart'>Back to cart</a> or <input class="shopbutton"type='submit' name='submit' value='continue'>
        </div>
    </fieldset>

</form>
