<?php echo form_open("$controller/save",array('class'=>'save-form')); ?>

<label for="zone_id">National Zone Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-group">
        <select name="national_zone_id" class="form-control" data-placeholder="Select Zone" id="national_zone_id">
            <option value="">Select National Zone</option>
        </select>
    </div>
</div>

<label for="zone_name">Zone Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="zone_name" name="zone_name" class="form-control" autocomplete="off" placeholder="Zone Name">
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