<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2><?= $pg_title ?></h2>
			</div>

			<div class="body">
				<?php echo form_open("$controller/save",array('id'=>'save-form')); ?>
					<input type="hidden" name="u_type" value="<?php echo $u_type ?>" />

					<label class="form-label"><?php echo strtoupper($u_type) ?> Name<span class="required">*</span></label>
					<div class="form-group">
						<div class="form-line">
							<input type="text" name="users_name" class="form-control" />
						</div>
					</div>

					<label class="form-label">HO Mobile<span class="required">*</span></label>
					<div class="form-group">
						<div class="form-line">
							<input type="text" name="users_mobile" class="form-control" value="" maxlength="10"/>
						</div>
					</div>

					<label class="form-label">Password<span class="required">*</span></label>
					<div class="form-group">
						<div class="form-line">
							<input type="text" name="users_password" class="form-control" />
						</div>
					</div>

					<input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Save" />

			        <a href="<?php echo base_url("$controller/lists/all/$u_type?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">Cancel</a>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>	