<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="area_id" value="<?php echo $info[0]['area_id']; ?>" />

<label for="zone_id">Zone Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-group">
        <select name="zone_id" class="form-control" data-placeholder="Select Zone" id="zone_id">
            <option value="<?php echo $info[0]['zone_id']; ?>" selected="selected"><?php echo $info[0]['zone_name']; ?></option>
        </select>
    </div>
</div>

<label for="region_id">Region Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-group">
        <select name="region_id" class="form-control" data-placeholder="Select Region" id="region_id">
            <option value="<?php echo $info[0]['region_id']; ?>" selected="selected"><?php echo $info[0]['region_name']; ?></option>
        </select>
    </div>
</div>

<label for="region_name">Area Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="area_name" name="area_name" class="form-control" autocomplete="off" placeholder="Area Name" value="<?php echo $info[0]['area_name']; ?>">
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