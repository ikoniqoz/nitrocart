<?php
    if (validation_errors())
        {
             echo "<div class='errors'>" . validation_errors() . "</div>";
        }
?>
{{if addresses}}
    <h2>Shipping address</h2>

            <form name="form1" action="{{x:uri}}/checkout/shipping/" method="POST">
                <input type='hidden' value='existing' name='selection'>
                <fieldset>
                    <h3>Select an existing address</h3>
                    <table>
                        {{addresses}}
                            <tr>
                                <td>
                                    <input type="radio" name="address_id" value="{{id}}">
                                </td>
                                <td>
                                    {{address1}}, {{address2}}
                                </td>
                                <td>
                                    {{city}} {{country}}
                                </td>
                                <td>
                                    {{state}} {{zip}}
                                </td>
                            </tr>
                         {{/addresses}}
                    </table>
            </fieldset>
            <fieldset>

                <div  class="buttons">
                    <a class="shopbutton" href='{{x:uri}}/cart'>{{ helper:lang line="nitrocart:messages:checkout:back_to_cart" }}</a> or <input class="shopbutton"type='submit' name='submit' value='continue'>
                </div>

            </fieldset>
           </form>
           <hr />
        {{endif}}


<form name="form2" action="{{x:uri}}/checkout/shipping/" method="POST">
    <input type='hidden' value='new' name='selection'>
    <fieldset>
        <h2>New Address</h2>

        <ul class="two_column">
            <li>
                <label>First name <span class="required">*</span></label>
                <input type="text" name="first_name" value="{{first_name}}">
            </li>
            <li>
                <label>Last Name <span class="required">*</span></label>
                <input type="text" name="last_name" value="{{last_name}}">
            </li>
            <li>
                <label>Email <span class="required">*</span></label>
                <input type="text" name="email" value="{{email}}">
            </li>
            <li>
                <label>Company <span class="required">*</span></label>
                <input type="text" name="company" value="{{company}}">
            </li>
            <li>
                <label>Phone <span class="required">*</span></label>
                <input type="text" name="phone" value="{{phone}}">
            </li>
            <li>
                <label>address1 <span class="required">*</span></label>
                <input type="text" name="address1" value="{{address1}}">
            </li>
            <li>
                <label>address2</label>
                <input type="text" name="address2" value="{{address2}}">
            </li>
            <li>
                <label>City <span class="required">*</span></label>
                <input type="text" name="city" value="{{city}}">
            </li>
            <li>
                <label>Country</label>
                {{x:countries class='nc_country_select' name='country' }}

            </li>
            <li>
                <label>State <span class="required">*</span></label>
                {{x:states class='nc_states_select' name='state' init='yes' }}

            </li> 
            <li>
                <label>ZIP/Postcode <span class="required">*</span></label>
                <input type="text" name="zip" value="{{zip}}">
            </li>
        </ul>



    </fieldset>


    <fieldset>

        <div class="buttons">
            <a class="shopbutton" href='{{x:uri}}/cart'>back to cart</a> or <input class="shopbutton"type='submit' name='submit' value='continue'>
        </div>

    </fieldset>

</form>