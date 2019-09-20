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
				<input type="hidden" name="users_zone_id" class="fill" value="<?php echo $info[0]->users_zone_id; ?>" />

                <label class="form-label">Select ASM<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<select name="parent_id" class="form-control" data-placeholder="Select ASM" id="reporting-mgr" data-role="asm">
							<option></option>
                            <option value="<?php echo $info[0]->users_parent_id; ?>" selected="selected"><?php echo $info[0]->mgr_name; ?></option>
						</select>
					</div>
				</div>

				<label class="form-label">Area<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<input type="text" name="area_name" class="form-control fill" disabled="disabled" value="<?php echo $info[0]->area_name; ?>"/>
                        <input type="hidden" name="users_zone_id" class="fill" value="<?php echo $info[0]->users_zone_id; ?>" />
                        <input type="hidden" name="users_area_id" class="fill" value="<?php echo $info[0]->users_area_id; ?>"/>
                        <input type="hidden" name="users_region_id" class="fill" value="<?php echo $info[0]->users_region_id; ?>"/>
                    </div>
				</div>

				<label class="form-label">City<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<select name="users_city_id" class="form-control" data-placeholder="Select City" id="city_id" value="<?php echo $info[0]->city_name; ?>">
							<option></option>
                            <option value="<?php echo $info[0]->users_city_id; ?>" selected="selected"><?php echo $info[0]->city_name; ?></option>
						</select>
						<input type="hidden" name="user_record" />
					</div>
				</div>

				<label class="form-label">MR Name<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<input type="text" name="users_name" class="form-control" value="<?php echo $info[0]->users_name; ?>" />
					</div>
				</div>

				<label class="form-label">MR Mobile<span class="required">*</span></label>
				<div class="form-group">
					<div class="form-line">
						<input type="text" name="users_mobile" maxlength="10" class="form-control" value="<?php echo $info[0]->users_mobile; ?>" />
					</div>
				</div>

				<!-- <label class="form-label">Password<span class="required">*</span></label>
				<div class="form-group">
                    <div class="form-line">
						<input type="text" name="users_password" value="<?php echo $info[0]->users_password; ?>" class="form-control" />
					</div>
				</div> -->

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