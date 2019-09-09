<?php echo form_open("login/user/save",array('class'=>'save-form')); ?>
<label class="form-label">Password<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="password" id="password" name="password" class="form-control" autocomplete="off" placeholder="password">
    </div>
</div>

<label class="form-label">Re-Enter Password<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="password" id="re_password" name="re_password" class="form-control" autocomplete="off" placeholder="re-password">
    </div>
</div>

<input type="hidden" id="users_id" name="users_id" value="<?php echo $users_id; ?>">

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <!-- <span>Save</span> -->
</button>

<?php echo form_close(); ?>