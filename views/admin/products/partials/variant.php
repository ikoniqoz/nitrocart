       
        <div class="buttons">
            <a class='button blue clear_all_variance_data' href='#'>Close</a>

            <a class='button modal' href='http://localhost/nitrocms/admin/nitrocart/variances/shipping/get/{{variant.id}}'>Shipping <i class='icon-briefcase'></i></a>
         
        </div>

        <input type="hidden" name="id" id="id" value="{{id}}" />



            <fieldset>    
                <h3>Variant Details {  {{variant.name}}  }</h3>           
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
 


        <div class="buttons">
            <a class='button blue clear_all_variance_data' href='#'>Close</a>
        </div>
