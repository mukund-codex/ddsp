<?php echo form_open("speciality_category/save",array('class'=>'save-form')); ?>

<label class="form-label">Speciality Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <select name="speciality_id" class="form-control" data-placeholder="Select Category" id="speciality_id">
            <option></option>
        </select>
    </div>
</div>

<label class="form-label">Category Name<span class="required">*</span></label>
<div class="form-group">
    <div class="form-line">
        <input type="text" id="category_name" name="category_name" class="form-control" autocomplete="off" placeholder="Category Name">
    </div>
</div>

<label class="form-label">Category Type<span class="required">*</span></label>
<div class="demo-radio-button">
    <input name="category_type" type="radio" id="radio_1" checked="" value="rxn">
    <label for="radio_1" style="min-width: 70px;">RXN</label>
    <input name="category_type" type="radio" id="radio_2" value="strips">
    <label for="radio_2">Strips</label>
</div>

<button type="submit" class="btn btn-primary m-t-15 waves-effect">
    <i class="material-icons">save</i>
    <span>Save</span>
</button>

<a href="<?php echo base_url("molecule/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">
    <i class="material-icons">reply_all</i>	
    <span>Cancel</span>
</a>
<?php echo form_close(); ?>

<?php echo $this->load->view('template/popup/popup-box') ?>