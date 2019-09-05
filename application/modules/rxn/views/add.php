<?php echo form_open("rxn/save",array('class'=>'save-form')); ?>
<label class="form-label">Brand Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="brand_id" class="form-control" data-placeholder="Select Brand" id="brand_id">
            <option></option>
        </select>
    </div>
</div>

<label class="form-label">SKU ?</label><br>
<div class="demo-radio-button">
    <input name="group1" type="radio" id="skuYes" checked="" value="yes">
    <label for="skuYes" style="min-width:60px;">Yes</label>
    <input name="group1" type="radio" id="skuNo" value="no">
    <label for="skuNo">No</label>
</div><br>

<label class="form-label" id="skuLabel">SKU</label>
<div class="form-group" id="skuDiv">
    <div class="form-line">
        <select name="sku_id" class="form-control" data-placeholder="Select SKU" id="sku_id">
            <option></option>
        </select>
    </div>
</div>

<label class="form-label">RXN/Week<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="rxn" name="rxn" class="form-control" autocomplete="off" placeholder="RXN/Week">
    </div>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("rxn/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>