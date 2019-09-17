<?php echo form_open_multipart("communication/save",array('class'=>'save-form')); ?>

<label for="doctor_id">Send Notification To:</label>
<div class="form-group">
    <div class="demo-radio-button">
        <input name="sms_group" type="radio" class="with-gap radio-col-blue" id="radio_3" value="group">
        <label for="radio_3">GROUP</label>
        <input name="sms_group" type="radio" id="radio_4" class="with-gap radio-col-blue" value="single">
        <label for="radio_4">INDIVIDUAL</label>
    </div>
</div>

<div id="group-select-form">
    <label for="group_id">Select Group</label>
    <div class="form-group">
        <div class="form-line">
            <select name="group_id" class="form-control" data-placeholder="Select Group" id="group_id">
                <option value="">Select Group</option>
                <option value="ZSM">ZSM</option>
                <option value="ASM">ASM</option>
                <option value="MR">MR</option>
            </select>
        </div>
    </div>
</div>

<div id="fill_records" style="display:none">
    <label for="doctor_id">Select <span id="role_label"></span></label>
    <div class="form-group">
        <div class="form-line">
            <select name="selected_roles[]" class="form-control" data-placeholder="Select Names(s)" id="selected_roles">
            </select>
        </div>
    </div>
</div>

<div id="patient_records" style="display:none">
    <label for="patient_id">Select Patient</label>
    <div class="form-group">
        <div class="form-line">
            <select name="selected_patients[]" class="form-control" data-placeholder="Select Patient(s)" id="selected_patients" multiple="mutiple">
                <option value=""></option>
            </select>
        </div>
    </div>
</div>

<label class="form-label">Title<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="title" name="title" class="form-control" autocomplete="off" placeholder="Title">
    </div>
</div>

<label class="form-label">Description</label>
<div class="form-group">
    <div class="form-line">
        <textarea name="description" id="description" style="width:100%;height:150px;"></textarea>
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