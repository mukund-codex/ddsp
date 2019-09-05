<?php echo form_open("sku/save",array('class'=>'save-form')); ?>
<label class="form-label">Brand Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="brand_id" class="form-control" data-placeholder="Select Brand" id="brand_id">
            <option></option>
        </select>
    </div>
</div>

<label class="form-label">SKU<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="sku" name="sku" class="form-control" autocomplete="off" placeholder="SKU">
    </div>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("sku/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>