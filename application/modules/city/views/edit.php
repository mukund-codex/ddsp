<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="city_id" value="<?php echo $info[0]['city_id']; ?>" />

<label class="form-label">State<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="state_id" class="form-control" data-placeholder="Select State" id="state_id">
            <option value="<?php echo $info[0]['state_id']; ?>" selected="selected"><?php echo $info[0]['state']; ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <div class="form-line">
        <input type="text" id="city_name" name="city_name"  value="<?php echo $info[0]['city_name']; ?>" class="form-control" autocomplete="off" placeholder="City Name">
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