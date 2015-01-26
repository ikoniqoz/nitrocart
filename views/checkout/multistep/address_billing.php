    <?php
        if (validation_errors()) 
            {
                echo "<div class='alert alert-danger'>" . validation_errors() . "</div>";
            }
    ?>
    </div> 
</div>  
<div class="row">

    {{if addresses}}
    <div class="col-sm-6">
        <h2>Billing address</h2>

        <form name="form1" action="{{x:uri}}/checkout/billing/" method="POST" >
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
                                {{address1}}
                                {{ if address2 }}
                                    <br>{{address2}}
                                {{ endif }}
                            </td>  
                            <td>
                                {{city}}    
                            </td>  
                            <td>
                                {{state}}  
                            </td>                                                                         
                            <td>
                                {{zip}} 
                            </td> 
                            <td>
                                {{ country_label }}  
                            </td> 
                        </tr>
                    {{/addresses}}  
                        <tr>
                            <td colspan='3'>Same for shipping ?</td>     
                            <td>
                                <input type="checkbox" name="sameforshipping" value="1">
                            </td> 
                                                                                                
                        </tr>       
                        <tr>
                            <td colspan='3'>
                                <a href="#">Agree to Terms and Conditions</a> 
                                <span class="required">*</span>
                            </td>                              
                            <td>
                                <input type="checkbox" name="useragreement" value="1">
                            </td> 
                                                                       
                        </tr>                      
                </table>
            </fieldset>
            <fieldset>

                <div class="buttons"> 
                    <a class="btn btn-default" href='{{x:uri}}/cart'>Back to Cart</a>&nbsp; or &nbsp;<input class="btn btn-default" type='submit' name='submit' value='continue'>
                </div>

            </fieldset>
        </form> 
    </div>  <!-- /col -->
    
    {{endif}}
    
    <div class="col-sm-5">
        <h2>New Billing Address</h2>
        <form name="form2" action="{{x:uri}}/checkout/billing/" method="POST" class="new-billing-address">
            <input type='hidden' value='new' name='selection'>
            <fieldset>

                <ul class="two-column">
                    <li>
                        <label>First name <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            <input type="text" name="first_name" value="{{first_name}}">
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>Last name <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            <input type="text" name="last_name" value="{{last_name}}">
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>Email <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            <input type="text" name="email" value="{{email}}">
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>Phone <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            <input type="text" name="phone" value="{{phone}}">
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>Company</label>
        <!--                <div class="input"> -->
                            <input type="text" name="company" value="{{company}}">
        <!--                </div>  -->
                    </li>            
                    <li>
                        <label>Address Line 1 <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            <input type="text" name="address1" value="{{address1}}">
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>Address Line 2</label>
        <!--                <div class="input"> -->
                            <input type="text" name="address2" value="{{address2}}">
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>City <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            <input type="text" name="city" value="{{city}}">
        <!--                </div>  -->
                    </li>

                    <li>
                        <label>Country</label>
        <!--                <div class="input"> -->
                            {{x:countries class='nc_country_select' name='country' }}
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>State <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            {{x:states class='nc_states_select' name='state' init='yes' }}
        <!--                </div>  -->
                    </li>                    
                    <li>
                        <label>ZIP/Postcode <span class="required">*</span></label>
        <!--                <div class="input"> -->
                            <input type="text" name="zip" value="{{zip}}">
        <!--                </div>  -->
                    </li>
                    <li>
                        <label>Is your Shipping Address the same ?</label>
        <!--                <div class="input"> -->
                            <input type="checkbox" name="sameforshipping" value="1">
        <!--                </div>  -->
                    </li> 
                    <li>
                        <label><a data-toggle="modal" href="{{ url:site }}#terms-and-conditions-modal">Agree to Terms and Conditions</a> <span class="required"> *</span></label>
        <!--                <div class="input"> -->
                            <input type="checkbox" name="useragreement" value="1">
        <!--                </div>  -->
                    </li>                         
                </ul>   

            </fieldset>

            <fieldset>

                <div class="buttons"> 
                    <a class="btn btn-default" href='{{x:uri}}/cart'>{{# helper:lang line="nitrocart:messages:checkout:back_to_cart" #}}Back to Cart</a> or <input class="btn btn-default" type='submit' name='submit' value='continue'>
                </div>

            </fieldset>

        </form>
    </div>   <!-- /col -->
</div>   <!-- /row -->