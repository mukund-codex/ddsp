<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="national_zone_id" value="<?php echo $info[0]['national_zone_id']; ?>" />

<label for="zone_name">National Zone Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="national_zone_name" name="national_zone_name" class="form-control" autocomplete="off" placeholder="National Zone Name" value="<?php echo $info[0]['national_zone_name']; ?>">
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