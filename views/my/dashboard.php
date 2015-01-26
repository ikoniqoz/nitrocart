<h3>
    Welcome to your shop dashboard
</h3>

<a href='{{x:uri}}/my'>Dashboard</a><br/>

<a href='{{x:uri}}/my/orders'>Orders</a><br/>

<a href='{{x:uri}}/my/addresses'>Addresses</a><br/>

<a href='{{x:uri}}/my/addresses/create'>Create new Addresses</a><br/>


{{shop_adina:field email='sal@inspiredgroup.com.au' get='membership_level' }}


{{if wishlist_enabled}}
<a href='{{x:uri}}/my/wishlist'>Wishlist</a><br/>
{{endif}}