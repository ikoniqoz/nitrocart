<h2>
    Create Address
</h2>

<?php echo (validation_errors()) ? validation_errors() : '' ?>

<form name="form2" action="{{x:uri}}/my/addresses/create" method="POST">

    <fieldset>
        <h3>New Address</h3>

        <ul class="two_column">
            <li>
                <label>First Name<span>*</span></label>
                <div class="input">
                    <input type="text" name="first_name" value="{{first_name}}">
                </div>
            </li>
            <li>
                <label>Surname<span>*</span></label>
                <div class="input">
                    <input type="text" name="last_name" value="{{last_name}}">
                </div>
            </li>
            <li>
                <label>Email<span>*</span></label>
                <div class="input">
                     <input type="text" name="email" value="{{email}}">
                </div>
            </li>
            <li>
                <label>Company</label>
                <div class="input">
                     <input type="text" name="company" value="{{company}}">
                </div>
            </li>
            <li>
                <label>Phone<span>*</span></label>
                <div class="input">
                    <input type="text" name="phone" value="{{phone}}">
                </div>
            </li>
            <li>
                <label>Address Line 1<span>*</span></label>
                <div class="input">
                    <input type="text" name="address1" value="{{address1}}">
                </div>
            </li>
            <li>
                <label>Address Line 2<span></span></label>
                <div class="input">
                     <input type="text" name="address2" value="{{address2}}">
                </div>
            </li>
            <li>
                <label>City<span>*</span></label>
                <div class="input">
                    <input type="text" name="city" value="{{city}}">
                </div>
            </li>

            <li>
                <label>Country</label>
                <div class="input">
                    {{x:countries name='country'}}
                </div>
            </li>
            <li>
                <label>State</label>
                <div class="input">
                    {{x:states name='state'}}
                </div>
            </li>            
            <li>
                <label>Zip/Postcode<span>*</span></label>
                <div class="input">
                    <input type="text" name="zip" value="{{zip}}">
                </div>
            </li>
            <li>
                <label>Address type<span></span></label>
                <div class="input">
                    <select name='address_type'>
                        <option value='2'>Billing and Shipping</option>
                        <option value='1'>Billing only</option>
                        <option value='0'>Shipping only</option>
                    </select>
                </div>
            </li>
            <li>
                <label></label>
                <div class="input">
                    <input type="checkbox" name="useragreement" value="1">Agree to Terms and Conditions
                </div>
            </li>
        </ul>



    </fieldset>


    <fieldset>

        <div>
            <span style="float: right;">
                <input type='submit' name='submit' value='Save'>
            </span>
        </div>

    </fieldset>

</form>