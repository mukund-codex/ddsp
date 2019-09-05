<div class="modal fade in" id="uploadbox" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
			<?php echo form_open("$controller/uploadcsv",array('id'=>'uploadForm', 'name'=>'frm_delete')); ?>

            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Upload CSV File</h4>
            </div>

            <div class="modal-body">
            	<div class="row" style="margin-bottom: 25px">
                	<div class="col-sm-3"><b>CSV columns: </b></div>
                	<div class="col-sm-9"><?php echo implode('<span class="seperator"> | </span>', $csv_fields) ?></div>
                </div>
				
				<div class="form-group form-float">
					<div class="form-line focused">
						<input type="file" name="csvfile" class="form-control textfield" />
					</div>
				</div>

				<div id="show_msg" class="show_msg" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="upload-btn" class="btn btn-link waves-effect">Upload</button>
                <button type="button" class="btn btn-link waves-effect reset-upload" data-dismiss="modal">CLOSE</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>