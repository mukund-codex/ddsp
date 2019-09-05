<?php echo form_open("coupon/save",array('class'=>'save-form')); ?>
<label class="form-label">Bunch Code<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="bunch_id" class="form-control" data-placeholder="Select Bunch Code" id="bunch_id">
            <option value="">Select Bunch Code</option>
        </select>
    </div>
</div>

<label class="form-label">Coupon Code<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="tel" id="code" name="code" class="form-control" autocomplete="off" placeholder="Coupon Code">
    </div>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("language/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>