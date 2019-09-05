<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="doctor_id" value="<?php echo $info[0]['doctor_id']; ?>" />
<input type="hidden" name="doctor_users_id" id="doctor_users_id" class="form-control" maxlength="50" value="<?php echo $info[0]['doctor_users_id']; ?>" />
<label class="form-label">City<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="users_city_id" class="form-control" data-placeholder="Select City" id="users_city_id">
            <option value="<?php echo $info[0]['city_id']; ?>" selected="selected"><?php echo $info[0]['city_name']; ?></option>
        </select>
    </div>
</div>

<label class="form-label">Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
    	<input type="text" name="users_name" id="users_name" value="<?php echo $info[0]['users_name']; ?>" class="form-control" maxlength="50" readonly disabled />
    </div>
</div>

<div class="form-group">
    <div class="form-line">
        <input type="text" id="doctor_name" name="doctor_name"  value="<?php echo $info[0]['doctor_name']; ?>" class="form-control" autocomplete="off" placeholder="Doctor Name">
    </div>
</div>

<div class="form-group">
    <div class="form-line">
        <input type="text" id="doctor_mobile_no" name="doctor_mobile_no" value="<?php echo $info[0]['doctor_mobile_no']; ?>" class="form-control" autocomplete="off" placeholder="Doctor Mobile No." maxlength="10">
    </div>
</div>


<div class="form-group">
    <div class="form-line">
        <input type="text" id="doctor_code" name="doctor_code" value="<?php echo $info[0]['doctor_code']; ?>" class="form-control" autocomplete="off" placeholder="Doctor Code">
    </div>
</div>

<div class="form-group">
    <div class="form-line">
        <input type="text" id="doctor_speciality" name="doctor_speciality" value="<?php echo $info[0]['doctor_speciality']; ?>" class="form-control" autocomplete="off" placeholder="Doctor Speciality">
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