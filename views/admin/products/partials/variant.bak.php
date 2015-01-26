<input type="hidden" name="static_product_id" id="static_product_id" data-pid="{{id}}" />

{{name}}

    <div class="content">

        <?php echo form_open(NC_ADMIN_ROUTE.'/product/edit/{{id}}', 'class="crud"'); ?>

        <input type="hidden" name="id" id="id" value="{{id}}" />

        <div class="tabs">

            <fieldset>      
                <h3>Product</h3>           
                <table>
                    <tr>   <td>ID</td>              <td>{{id}}</td>   </tr>
                    <tr>   <td>Name</td>            <td>{{name}}</td>   </tr>
                    <tr>   <td>Product Type</td>    <td><?php echo nc_product_type_name($type_id);?></td>        </tr>

                    <tr>   <td>Slug</td>            <td><a target='new' href='{{url:site}}<?php echo NC_ROUTE;?>/products/product/{{slug}}'>{{slug}}</a></td>     </tr>
                    <tr>   <td>Code</td>            <td>{{code}}</td>   </tr>

                    <tr>   <td>View Count</td>      <td>{{views}}</td>     </tr>
                    <tr>   <td>Date Created</td>    <td>{{created}}</td>     </tr>
                    
                    <tr>   <td>Tax</td>             <td><a target='new' href='{{x:uri x='ADMIN'}}/tax/edit/{{tax_id}}'>{{tax_id}} | View Tax Record</a></td>        </tr>
                    <tr>   <td>Admin Product Page</td><td><a target='new' href='{{x:uri x='ADMIN'}}/product/edit/{{slug}}'>View Admin</a></td>     </tr>
                    <tr>   <td>Store Front Page</td> <td><a target='new' href='{{x:uri}}/products/product/{{slug}}'>View  ( {{x:uri}}/products/product/{{slug}} )</a></td>     </tr>                    
                </table>
            </fieldset>

            <fieldset>    
                <h3>Variant</h3>           
                <table>
                    <tr>   <td>ID</td>              <td>{{variant.id}}</td>             </tr>
                    <tr>   <td>Name</td>            <td>{{variant.name}}</td>           </tr>
                    <tr>   <td>SKU</td>              <td>{{variant.sku}}</td>          </tr>  
                    <tr>   <td>On Hand</td>              <td>{{variant.sk_on_hand}}</td>          </tr>  

                    <tr>   <td>MIN QTY</td>         <td>{{variant.min_qty}}</td>        </tr>

                    <tr>   <td>Price</td>           <td>{{variant.price}}</td>          </tr>   
                    <tr>   <td>RRP</td>             <td>{{variant.rrp}}</td>            </tr> 
                    <tr>   <td>Base</td>            <td>{{variant.base}}</td>           </tr>          

                    <tr>   <td>Available</td>    <td><?php echo yesNoBOOL($variant->available);?></td>   </tr>                               
                    <tr>   <td>Discountable</td>    <td><?php echo yesNoBOOL($variant->discountable);?></td>   </tr>
                    <tr>   <td>Is Shippable</td>    <td><?php echo yesNoBOOL($variant->is_shippable);?></td>   </tr>  


                    <!--  
                          
                    <tr>   <td>Is Digital</td>      <td><?php echo yesNoBOOL($variant->is_digital);?></td>   </tr>
                        -->
                    <tr>   <td>Height</td>          <td>{{variant.height}}</td>         </tr>    
                    <tr>   <td>Width</td>           <td>{{variant.width}}</td>          </tr>    
                    <tr>   <td>Length</td>          <td>{{variant.length}}</td>         </tr>    
                    <tr>   <td>Weight</td>          <td>{{variant.weight}}</td>         </tr>     

                    <tr>   <td>Package Group</td>   <td><?php echo nc_get_package_group_name($variant->pkg_group_id);?></td>   </tr>        
                    <tr>   <td>Shipping Zone</td>   <td><?php echo nc_get_zone_name($variant->zone_id);?></td>   </tr>                     
                </table>
            </fieldset>
          
            <fieldset>    
                <h3>Attributes</h3>           
                <table>
                    {{attributes}}
                    <tr>   <td>{{e_label}}</td>      <td>{{e_value}}</td>   </tr>
                    {{/attributes}}
                </table>
            </fieldset>
 
        </div>

        <div class="buttons">
            <a class='btn blue clear_all_variance_data' href='#'>Close</a>
        </div>

        <?php echo form_close(); ?>

    </div>
