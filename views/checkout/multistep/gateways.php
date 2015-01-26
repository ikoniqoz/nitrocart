<h2 id="page_title">Select payment method</h2>

<form action="{{x:uri}}/checkout/gateway" method="POST" name="form_gateways">

    <fieldset>
        <ul>
        {{gateways}}
            <!--img src="{{image}}"-->
           <li><input type="radio" value="{{id}}" name="gateway_id" checked>{{title}}</li>
        {{/gateways}}
        </ul>
    </fieldset>

    <fieldset>    
        <div class="buttons"> 
            <a class="shopbutton" href='{{x:uri}}/cart'>{{ helper:lang line="nitrocart:messages:checkout:back_to_cart" }}</a> or <input class="shopbutton"type='submit' name='submit' value='continue'>
        </div>
    </fieldset>

</form>
