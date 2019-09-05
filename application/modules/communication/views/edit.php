<?php echo form_open("$controller/modify",array('class'=>'save-form')); ?>
<input type="hidden" name="c_id" value="<?php echo $info[0]['c_id']; ?>" />

<label class="form-label">Title<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="title" name="title" class="form-control" autocomplete="off" placeholder="Title" value="<?php echo $info[0]['title']; ?>">
    </div>
</div>

<label class="form-label">Description</label>
<div class="form-group">
    <div class="form-line">
        <textarea name="description" id="description" style="width:100%;height:150px;"><?php echo $info[0]['description']; ?></textarea>
    </div>
</div>

<label class="form-label">Image</label>
<div class="form-group">
    <div class="form-line">
        <input type="file" name="images[]" id="images" multiple="multiple" class="form-control" accept="image/*" >
    </div>
</div>

<label class="form-label">Document</label>
<div class="form-group">
    <div class="form-line">
        <input type="file" name="document" id="document" class="form-control" accept="application/pdf" >
        <input type="hidden" name="media_type2" id="media_type2" value="document" >
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