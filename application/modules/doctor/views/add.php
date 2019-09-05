<?php echo form_open("doctor/save",array('class'=>'save-form')); ?>
<label class="form-label">Doctor Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="doctor" name="doctor" class="form-control" autocomplete="off" placeholder="Doctor Name">
    </div>
</div>

<label class="form-label">Doctor Mobile No.<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="tel" id="mobile" name="mobile" class="form-control" autocomplete="off" placeholder="Doctor Mobile No." maxlength="10">
    </div>
</div>

<label class="form-label">Message (Max 250 Characters)<span class="required">*</span></label>
<div class="form-group">
    <textarea name="message" id="message" cols="30" rows="10" class="form-control max-count" data-max="255" maxlength="255" style="border: 1px solid rgb(35, 63, 139); padding: 10px"></textarea>
</div>

<div class="form-group">
    <label for="doctor_photo">Doctor Photo <span class="required">*</span></label>
    <div class="clearfix" >
        <div class="dropBox" style="float:left;position:relative">
            <div class="loadpicture"></div>
            <input type="file" id="doctor_photo" name="doctor_photo" class="uploadphoto" style="width: 250px;height: 250px">
            <input type="hidden" name="x1" value="" />
            <input type="hidden" name="y1" value="" />
            <input type="hidden" name="x2" value="" />
            <input type="hidden" name="y2" value="" />
        </div>
        
        <div class="preview" style="float:left;width:250px;position:relative">
            <div class="loadpicture"></div>
            <img style="width:100%" src="<?= base_url('assets/images/demo-img.jpg') ?>" id="selectArea">
            <input type="hidden" name="imageName" id="imageName" />
            <input type="hidden" name="posterName" id="posterName" />
        </div>
    </div>
    
    <div class="cropcontainer">
        <div class="btn-group" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-secondary actionbtn changebutton" title="Change">Change</button>
        </div>
    </div>
</div>

<a id="img_preview" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">crop_original</i>
    <span>Preview</span>
</a>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("doctor/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>