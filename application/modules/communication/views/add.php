<?php echo form_open_multipart("communication/save",array('class'=>'save-form')); ?>
<label class="form-label">Title<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="title" name="title" class="form-control" autocomplete="off" placeholder="Title">
    </div>
</div>

<label class="form-label">Description</label>
<div class="form-group">
    <div class="form-line">
        <textarea name="description" id="description" style="width:100%;height:150px;">Description</textarea>
    </div>
</div>

<label class="form-label">Image</label>
<div class="form-group">
    <div class="form-line">
        <input type="file" name="images[]" id="images" multiple="multiple" class="form-control" accept="image/*">
    </div>
</div>

<label class="form-label">Document</label>
<div class="form-group">
    <div class="form-line">
        <input type="file" name="document" id="document" class="form-control" accept="application/pdf">
    </div>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("communication/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>