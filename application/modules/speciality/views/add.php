<?php echo form_open("speciality/save",array('class'=>'save-form')); ?>
<label class="form-label">Speciality Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="speciality_name" name="speciality_name" class="form-control" autocomplete="off" placeholder="Speciality Name">
    </div>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("speciality/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>