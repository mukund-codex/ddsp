<?php echo form_open("$controller/save",array('class'=>'save-form')); ?>
	<label for="username">Name*</label>
	<div class="form-group">
		<div class="form-line">
			<input type="text" id="full_name" name="full_name" class="form-control" autocomplete="off" placeholder="Name">
		</div>
	</div>

	<label for="user_type">Role*</label>
	<div class="form-group">
		<div class="form-line">
			<select name="user_type" class="form-control" data-placeholder="Select Type" id="user_type">
				<option></option>
			</select>
		</div>
	</div>

	<label for="username">User Name*</label>
	<div class="form-group">
		<div class="form-line">
			<input type="text" id="username" name="username" class="form-control" autocomplete="off" placeholder="Username">
		</div>
	</div>

	<label for="password">Password*</label>
	<div class="form-group">
		<div class="form-line">
			<input type="password" id="password" name="password" class="form-control" autocomplete="off" placeholder="Password">
		</div>
	</div>

	<label for="email">Email</label>
	<div class="form-group">
		<div class="form-line">
			<input type="text" id="email" name="email" class="form-control" autocomplete="off" placeholder="abc@example.com">
		</div>
	</div>

	<label for="mobile">Mobile</label>
	<div class="form-group">
		<div class="form-line">
			<input type="text" id="mobile" name="mobile" class="form-control" autocomplete="off" placeholder="xxxxxxxxxx">
		</div>
	</div>

	<input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Save" />

	<a href="<?php echo base_url("$controller/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">Cancel</a>
<?php echo form_close(); ?>