<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="sc_id" value="<?php echo $info[0]['sc_id']; ?>" />

<label class="form-label">Category Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="speciality_id" class="form-control" data-placeholder="Select Speciality" id="speciality_id">
        <option value="<?php echo $info[0]['speciality_id']; ?>" selected="selected"><?php echo $info[0]['speciality_name']; ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <div class="form-line">
        <input type="text" id="category_name" name="category_name"  value="<?php echo $info[0]['category_name']; ?>" class="form-control" autocomplete="off" placeholder="Category Name">
    </div>
</div>

<label class="form-label">Category Type<span class="required">*</span></label>
<div class="demo-radio-button">
    <input name="category_type" type="radio" id="radio_1" value="rxn" <?php if($info[0]['category_type'] == 'rxn'){ echo "checked"; } ?>>
    <label for="radio_1" style="min-width: 70px;">RXN</label>
    <input name="category_type" type="radio" id="radio_2" value="strips" <?php if($info[0]['category_type'] == 'strips'){ echo "checked"; } ?>>
    <label for="radio_2">Strips</label>
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