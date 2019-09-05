<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="sku_id" value="<?php echo $info[0]['sku_id']; ?>" />

<label for="zone_id">Brand Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-group">
        <select name="brand_id" class="form-control" data-placeholder="Select Brand" id="brand_id">
            <option value="<?php echo $info[0]['brand_id']; ?>" selected="selected"><?php echo $info[0]['brand_name']; ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <div class="form-line">
        <input type="text" id="sku" name="sku"  value="<?php echo $info[0]['sku']; ?>" class="form-control" autocomplete="off" placeholder="SKU">
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