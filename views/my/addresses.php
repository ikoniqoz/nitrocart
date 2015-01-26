<h2 id="nc-view-title">
    Addresses
</h2>

<?php echo (validation_errors()) ? validation_errors() : '' ?>


    <a href='{{x:uri}}/my/addresses/create/'>Create</a>

    <?php if (count($items)) : ?>
            <table>
                    <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Billing</th>
                                <th>Shipping</th>
                                <th>Action</th>
                            </tr>
                    </thead>
                    <tbody>
                        {{items}}
                            <tr>
                                <td>{{id}}</td>
                                <td>{{first_name}} {{last_name}}</td>
                                <td>{{company}}</td>
                                <td>{{email}}</td>
                                <td>{{address1}} {{address2}}, {{zip}} {{city}}</td>
                                <td>
                                   {{x:yesno value=billing}} 
                                </td>
                                <td>
                                   {{x:yesno value=shipping}}                                    
                                </td>
                                <td>
                                    <a href="{{x:uri}}/my/addresses/delete/{{id}}" class="confirm button" > &times; Delete</a>
                                </td>
                            </tr>
                        {{/items}}
                    </tbody>
            </table>
    <?php else: ?>
            <div>Back to addresses</div>
    <?php endif; ?>

    <p>
        <a href="{{x:uri}}/my" class="button">Back to Dashboard</a>
    </p>
