<?php echo form_open("$controller/save",array('class'=>'save-form')); ?>
    <label class="form-label">Logo (Optional)</label>
    <div class="form-group">
        <div class="form-line">
            <input type="file" id="logo" name="logo" class="form-control">
        </div>
    </div>

    <label class="form-label">Application Name*</label>
    <div class="form-group">
        <div class="form-line">
            <input type="text" id="app_name" name="app_name" class="form-control" autocomplete="off" value="<?= $settings['app_name'] ?>">
        </div>
    </div>

    <label class="form-label">Theme</label>
    <div class="form-group">
        <div class="form-line">
            <select name="theme" class="form-control">
                <?php foreach($themes as $key=> $value): if(!empty($settings['theme'])): ?>
                <option value="<?= $value ?>" <?= ($value == $settings['theme']) ? "selected" : "" ?>><?= ucwords($value) ?></option>
                <?php else: ?>
                <option value="<?= $value ?>" <?= ($value == 'blue') ? "selected" : "" ?>><?= ucwords($value) ?></option>
                <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <label class="form-label">Sender ID (Optional)</label>
    <div class="form-group">
        <div class="form-line">
            <input type="text" id="sender_id" name="sender_id" class="form-control" autocomplete="off" value="<?= $settings['sender_id'] ?>">
        </div>
    </div>

    <input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Save" />

    <a href="<?php echo base_url("dashboard/admin?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">Cancel</a>
<?php echo form_close(); ?>