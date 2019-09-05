<?php echo form_open("$controller/save",array('class' => 'save-form')); ?>
<label class="form-label">Zone<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="users_zone_id" class="form-control" data-placeholder="Select Zone" id="zone_id">
            <option></option>
        </select>
    </div>
</div>

<label class="form-label">Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_name" class="form-control" maxlength="50" />
    </div>
</div>

<label class="form-label">Mobile</label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_mobile" class="form-control" maxlength="10"/>
    </div>
</div>

<label class="form-label">Emp ID<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_emp_id" class="form-control" maxlength="10"/>
    </div>
</div>

<label class="form-label">Password<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" name="users_password" class="form-control" maxlength="20"/>
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