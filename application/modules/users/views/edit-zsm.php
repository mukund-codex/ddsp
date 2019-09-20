<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2><?= $pg_title ?></h2>
			</div>

			<div class="body">
			<?php if(sizeof($info)) : ?>
				<?php echo form_open("$controller/modify",array('id'=>'save-form')); ?>
				<input type="hidden" name="users_id" value="<?php echo $info[0]->users_id; ?>" />
				<input type="hidden" name="u_type" value="<?php echo $u_type ?>" />

				<label class="form-label">Zone<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<select name="users_zone_id" class="form-control" data-placeholder="Select Zone" id="zone_id">
							<option></option>
							<option value="<?php echo $info[0]->users_zone_id ?>" selected="selected"><?php echo $info[0]->zone_name ?></option>
						</select>
						<input type="hidden" name="user_record" />
					</div>
				</div>

				<label class="form-label">ZSM Name<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<input type="text" name="users_name" class="form-control" value="<?php echo $info[0]->users_name; ?>" />
					</div>
				</div>

				<label class="form-label">ZSM Mobile<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<input type="text" name="users_mobile" maxlength="10" class="form-control" value="<?php echo $info[0]->users_mobile; ?>" />
					</div>
				</div>

				<input type="submit" class="btn btn-primary noreset" id="banner_btn" value='Save'>
				<a href="<?php echo base_url("$controller/lists/all/$u_type?c=$timestamp") ?>" class="btn btn-danger">Cancel</a>

				<?php echo form_close() ?>
			<?php else: ?>
				<h4 class="mb">No Record Found !!!</h4>
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>