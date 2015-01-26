
<form name="" method="post" action="{{x:uri}}/checkout/customer">
    <fieldset>
            {{ nitrocart:settings }}

                {{if allow_guest==true}}
                        <div class="input">
                            <label>
                                <input type='radio' name='customer' value='guest' checked> Checkout as Guest account
                            </label>
                        </div>
                        <div class="input">
                            <label>
                                <input type='radio' name='customer' value='register' >
                                Register a new account
                            </label>
                        </div>
                {{else}}
                        <div class="input">
                            <label>
                                Click Continue to register a new account
                                <input type='hidden' name='customer' value='register'>
                            </label>
                        </div>
                {{endif}}

            {{ /nitrocart:settings }}

        <div class="buttons">
            <a href='{{x:uri}}/cart'>Back to Cart</a>
            <input type='submit' name='submit' value='continue'>
        </div>
    </fieldset>
</form>

<hr />


<fieldset>

    <p>Or if you a returning customer, login here</p> <br />


    <form name="logincheckout" method="post" action="{{url:site}}users/login">

        <input type='hidden' name='redirect_to' value='{{x:uri}}/checkout'>

        <ul>
            <li>
                <label>email</label>
                <div class="input">
                    <input type='text' name='email' value='' style='' id="email" maxlength="120">
                </div>
            </li>
            <li>
                <label>password</label>
                <div class="input">
                    <input type='password' name='password' value='' style='' id="password" maxlength="20">
                </div>
            </li>
            <li>
                <label>
                    <input type='checkbox' name='remember' value='1'>
                    Remember Me
                </label>
            </li>

            <li class="links">
                <span id="reset_pass">
                     <a href='{{url:site}}users/reset_pass'>forgot your password ?</a>
                </span>
            </li>

        </ul>


        <div class="buttons">
            <button type="submit">
                Login
            </button>
        </div>
    </form>
</fieldset>
