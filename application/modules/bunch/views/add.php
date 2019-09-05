<?php echo form_open("bunch/save",array('class'=>'save-form')); ?>
<label class="form-label">Bunch Code<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="code" name="code" class="form-control" autocomplete="off" placeholder="Bunch Code">
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