<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="rxn_id" value="<?php echo $info[0]['rxn_id']; ?>" />

<label for="zone_id">Brand Name <span class="required">*</span></label>
<div class="form-group">
    <div class="form-group">
        <select name="brand_id" class="form-control" data-placeholder="Select Brand" id="brand_id">
            <option value="<?php echo $info[0]['brand_id']; ?>" selected="selected"><?php echo $info[0]['brand_name']; ?></option>
        </select>
    </div>
</div>

<label class="form-label">SKU ?</label><br>
<div class="demo-radio-button">
    <input name="group1" type="radio" id="skuYes" checked="" value="yes">
    <label for="skuYes" style="min-width:60px;">Yes</label>
    <input name="group1" type="radio" id="skuNo" value="no">
    <label for="skuNo">No</label>
</div><br><br>

<label for="zone_id" id="skuLabel">SKU</label>
<div class="form-group" id="skuDiv">
    <div class="form-group">
        <select name="sku_id" class="form-control" data-placeholder="Select SKU" id="sku_id">
            <option value="<?php echo $info[0]['sku_id']; ?>" selected="selected"><?php echo $info[0]['sku']; ?></option>
        </select>
    </div>
</div>

<div class="form-group">
    <div class="form-line">
        <input type="text" id="rxn" name="rxn"  value="<?php echo $info[0]['rxn']; ?>" class="form-control" autocomplete="off" placeholder="RXN">
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