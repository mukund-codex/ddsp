<?php echo form_open("city/save",array('class'=>'save-form')); ?>
<label class="form-label">State<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="state_id" class="form-control" data-placeholder="Select State" id="state_id">
            <option></option>
        </select>        
    </div>
</div>

<label class="form-label">City<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="city_name" name="city_name" class="form-control" autocomplete="off" placeholder="City Name">
    </div>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("city/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>