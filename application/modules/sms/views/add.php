<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2><?= $pg_title ?></h2>
			</div>

			<div class="body">
				<?php echo form_open("$controller/save",array('class'=>'save-form')); ?>

				<label for="doctor_id">Send SMS To:</label>
				<div class="form-group">
					<div class="demo-radio-button">
						<input name="sms_group" type="radio" class="with-gap radio-col-blue" id="radio_3" value="group">
						<label for="radio_3">GROUP</label>
						<input name="sms_group" type="radio" id="radio_4" class="with-gap radio-col-blue" value="single">
						<label for="radio_4">INDIVIDUAL</label>
					</div>
				</div>

				<div class="form-group">
					<label for="sms_type">Select SMS Type</label>
					<div class="form-group">
						<div class="form-line">
							<select name="sms_type" class="form-control" data-placeholder="Select SMS Type" id="sms_type">
								<option value="">Select Group</option>
								<option value="NON RESPONDANT REMINDER">Non Respondant Reminder</option>
								<option value="REPURCHASE REMINDER">Repurchase</option>
								<option value="REVISIT REMINDER">Revisit Reminder</option>
								<option value="HEALTH-TIPS">Heath Tips</option>
								<option value="TRACKING SMS">Tracking SMS</option>
							</select>
						</div>
					</div>
				</div>

				<div id="group-select-form">
					<label for="group_id">Select Group</label>
					<div class="form-group">
						<div class="form-line">
							<select name="group_id" class="form-control" data-placeholder="Select Group" id="group_id">
								<option value="">Select Group</option>
								<option value="HO">HO</option>
								<option value="ZSM">ZSM</option>
								<option value="RSM">RSM</option>
								<option value="ASM">ASM</option>
								<option value="MR">MR</option>
								<option value="DOCTOR">DOCTOR</option>
								<option value="PATIENT">PATIENT</option>
							</select>
						</div>
					</div>
				</div>

				<div id="fill_records" style="display:none">
					<label for="doctor_id">Select <span id="role_label"></span></label>
					<div class="form-group">
						<div class="form-line">
							<select name="selected_roles[]" class="form-control" data-placeholder="Select Names(s)" id="selected_roles">
								<option value=""></option>
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
				
				<label class="form-label">Message</label>
				<div class="form-group">
					<div class="form-line">
						<textarea id="message" name="message" class="form-control" cols="30" rows="10"></textarea>
					</div>
				</div>

                <input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Save" />

                <a href="<?php echo base_url("$controller/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">Cancel</a>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>