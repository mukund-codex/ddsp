<?php echo form_open("$controller/modify",array('class' => 'save-form')); ?>
<input type="hidden" name="users_id" value="<?php echo $info[0]['users_id']; ?>" />

<label class="form-label">Zone<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="users_zone_id" class="form-control" data-placeholder="Select Zone" id="zone_id">
            <option value="<?php echo $info[0]['zone_id']; ?>" selected="selected"><?php echo $info[0]['zone_name']; ?></option>
        </select>
    </div>
</div>

<label class="form-label">Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_name" class="form-control" maxlength="50" value="<?php echo $info[0]['users_name']; ?>" />
    </div>
</div>

<label class="form-label">Mobile</label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_mobile" class="form-control" maxlength="10" value="<?php echo $info[0]['users_mobile']; ?>"/>
    </div>
</div>

<label class="form-label">Emp ID<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_emp_id" class="form-control" maxlength="10" value="<?php echo $info[0]['users_emp_id']; ?>" />
    </div>
</div>

<label class="form-label">Password</label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_password" class="form-control" maxlength="20" value="<?php echo $info[0]['users_password']; ?>"/>
    </div>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("$controller/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>