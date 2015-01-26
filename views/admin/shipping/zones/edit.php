<div id="sortable">

	<div class="one_half" id="">

		<section class="title">

			<?php if(isset($id)): ?>
				<h4>Edit Shipping Zone</h4>
			<?php else: ?>
				<h4>Create Shipping Zone</h4>
			<?php endif; ?>
		</section>

		<?php if(isset($id)): ?>
			<?php echo form_open_multipart(NC_ADMIN_ROUTE.'/shipping_zones/edit/'.$id); ?>
		<?php else: ?>
			<?php echo form_open_multipart(NC_ADMIN_ROUTE.'/shipping_zones/create/'); ?>
		<?php endif; ?>



		<section class="item form_inputs">

			<div class="content">
				<fieldset>
					<ul>
						<li class="">
							<label for="name">Name<span>*</span></label>
							<div class="input">
								<?php echo form_input('name', set_value('name', $name), 'id="name"  placeholder="Enter package name here"'); ?>
							</div>
						</li>
						<li class="">
							<label for="default">Is Default<span></span></label>
							<div class="input"><?php echo form_dropdown('default', array(0=>'No',1=>'Yes'),  $default ); ?> </div>
						</li>	

					</ul>


				</fieldset>

				<div class="buttons">
					<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))); ?>
					<a href='{{x:uri x='ADMIN'}}/shipping_zones/' class='btn gray'>Cancel</a>
				</div>

			</div>

		</section>
		<?php echo form_close(); ?>

	</div>



			<?php if(isset($id)): ?>


			<div class="one_half last" id="" style='display:none'>

				<section class="title">

					<h4>Countries</h4>

				</section>


				<section class="item form_inputs">

					<div class="content">
						<fieldset>

							<form action='' name='' method=''>
								{{countries}}
								<input type='submit' value='Add'/>
							</form>


						</fieldset>

					</div>

				</section>

			</div>

			<?php endif; ?>

</div>