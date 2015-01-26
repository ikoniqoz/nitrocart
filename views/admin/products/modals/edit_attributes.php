<section class="title">
    <span>
        <h4>
            <em>{{product.name}}</em>: <span> { Attributes } </span>
        </h4>
    </span>
</section>

{{if has_attr}}

<section class="item">

    <div class="content">

			<fieldset>
				<?php echo form_open(NC_ADMIN_ROUTE.'/attributes/post_save/'.$variance->product_id.'/'.$variance->id);?>
						<div class="input">
							<h4>Attributes ( p:<?php echo $product->id;?> / v:<?php echo $variance->id;?> )</h4>
							<input name='product_id' type='hidden' value='{{product.id}}' >
							<input name='variance_id' type='hidden' value='{{variance.id}}' >
							<table>
								{{forms}}
								<tr>
									<td>
										<label for="">
											{{e_label}}
											<span></span>
										</label>
									</td>
									<td>
										<input name='e_attribute_id_{{id}}' type='text' value='{{e_value}}' placeholder='Required Value' >
									</td>
								</tr>
								{{/forms}}
							</table>
						</div>
						<br/>
						<input name='rename' type='checkbox'> Rename variance to attribute values
						<br/>
						<br/>
						<button class="btn blue" value="save" name="btnAction" type="submit">
							<span>Save</span>
						</button>
					<?php echo form_close();?>
			</fieldset>
	</div>
</div>
{{else}}

	<br/>
	<h2>This product has NO attributes..</h2>

	<br/>

{{endif}}
