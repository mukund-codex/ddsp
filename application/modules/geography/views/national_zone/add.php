<?php echo form_open("$controller/save",array('class'=>'save-form')); ?>
<label for="national_zone_name">National Zone Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="national_zone_name" name="national_zone_name" class="form-control" autocomplete="off" placeholder="National Zone Name">
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