<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2><?= $pg_title ?></h2>
			</div>
			
			<div class="body">
			<?php if(sizeof($info)) : ?>
				<?php echo form_open("$controller/modify",array('id'=>'save-form')); ?>
               	<input type="hidden" name="area_id" value="<?php echo $info[0]->area_id; ?>" />

				<div class="form-group form-float">
                    <div class="form-line">
                        <input type="text" id="area_name" name="area_name" class="form-control" autocomplete="off" value="<?php echo $info[0]->area_name; ?>">
                        <label class="form-label">Area Name</label>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary m-t-15 waves-effect" value="Save" />

                <a href="<?php echo base_url("$controller/lists?c=$timestamp") ?>" class="btn btn-danger m-t-15 waves-effect">Cancel</a>
				<?php echo form_close(); ?>
			<?php else: ?>
				<h4 class="mb">No Record Found !!!</h4>
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>