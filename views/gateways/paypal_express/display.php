<div id="MerchantPage">

	<form id="merchantForm" action="{{x:uri}/payment/process/{{order.id}}" method="POST">
	    <p>Please proceed to PayPal to complete and pay for your order</p>
	    <input type="hidden" value="d0n0tr3m0v3" name="postback">
	    <button type="submit" class="">Pay with PayPal</button>
	</form>

</div>