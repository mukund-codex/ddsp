<br>
<?php if(in_array('addSMSbalance', $permissions)) : ?>
<div class="block-header" style="margin-left:20px;">
	<h2>SMS USAGE</h2>
</div>
<div class="row clearfix" style="margin-left:5px;">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="info-box hover-zoom-effect">
			<div class="icon bg-deep-orange">
				<i class="material-icons">email</i>
			</div>
			<div class="content">
				<div class="text">TOTAL SMS</div>
				<div class="number"><?= number_format($total_balance[0]->balance); ?></div>
			</div>
		</div>

	</div>
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="info-box hover-zoom-effect">
			<div class="icon bg-blue">
				<i class="material-icons">email</i>
			</div>
			<div class="content">
				<div class="text">SMS REMAINING</div>
				<div class="number"><?= number_format($sms_balance); ?></div>
			</div>
		</div>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="info-box hover-expand-effect">
			<div class="icon bg-teal">
				<i class="material-icons">equalizer</i>
			</div>
			<div class="content">
				<div class="text">SMS USAGE</div>
				<div class="number"><?= number_format($total_balance[0]->balance - $sms_balance) ?></div>
			</div>
		</div>
	</div>
		
</div>
<?php endif; ?>

<a onclick="reset();" class="btn btn-primary m-t-15 waves-effect" style="margin-left:20px;">Reset Filters</a></li>

<?php if(in_array('download', $permissions)) : ?>
    <a href="<?php echo base_url("$download_url") ?>" class="btn btn-primary m-t-15 waves-effect" id="export" title="Export" style="float:right;margin-right:20px;">Export</a>
<?php endif; ?>
          
<div class="body table-responsive">
<?php if(isset($date_filters) && $date_filters): ?>
<div class="row clearfix">
    <div class="col-md-2">
        <label for="from_date">From Date: </label>
    </div>
    <div class="col-md-4">
        <input type="text" name="from_date" id="from_date" class="form-control filters" readonly="readonly" value="" autocomplete="off">
    </div>
    
    <div class="col-md-2">
        <label for="to_date">To Date: </label>
    </div>
    <div class="col-md-4">
        <input type="text" name="to_date" id="to_date" class="form-control filters" readonly="readonly" value="" autocomplete="off">
    </div>
</div>
<?php endif; ?>


<?php
$role = $this->session->get_field_from_session('role', 'user');
echo form_open("$controller/",array('id'=>'frm_delete', 'name'=>'frm_delete')); ?>
    <div class="double-scroll">
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <?php if(! isset($all_action) || $all_action || in_array('approve', $permissions) && $role != 'HO'): ?>
                    <th>
                        <input type="checkbox" name="" id="checkall" class="chk-col-<?= $settings['theme'] ?> filled-in">
                        <label for="checkall" style="margin:0; vertical-align:bottom"></label>
                    </th>
                    <?php endif ?>

                    <?php foreach ($columns as $headers) { ?>
                    <th class="font-bold"><?= $headers ?></th>
                    <?php } ?>
                    

                    <?php if(! isset($all_action) || $all_action ): ?>

                <?php if(in_array('edit', $permissions)) : ?>
                    <th class="font-bold"><i class="fa fa-edit"></i> Action</th>
                <?php endif; ?>
                
                    <?php endif; ?>
                </tr>
                
                <?php if(isset($show_filters) ? $show_filters : FALSE): ?>
                <tr>
                    <?php if(! isset($all_action) || $all_action ): ?>
                    <td>&nbsp;</td>
                    <?php endif; ?>

                    <?php foreach ($filter_columns as $filters): if(! count($filters)) { echo '<td></td>'; continue; } ?>
                    <td>
                        <?php if( isset($filters['show']) ? $filters['show'] : TRUE) : ?>
                            <input 
                                type="<?= (!isset($filters['type'])) ? 'text' : $filters['type'] ?>" 
                                name="<?= $filters['field_name'] ?>" 
                                id="<?= $filters['field_name'] ?>" 
                                placeholder="<?= $filters['field_label'] ?>" 
                                class="form-control filters" 
                                autocomplete="off" 
                            />
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                    
                    <?php if(! isset($all_action) || $all_action ): ?>
                    <td></td>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </thead>
            <tbody id="tbody">
                <?php echo $this->load->view($records_view); ?>
            </tbody>
        </table>
    </div>
    <?php if(isset($permissions) && count($permissions)) : if(in_array('remove', $permissions)) {  ?>
    <a class="btn btn-danger deleteAction" href="#" data-type="ajax-loader"><i class="material-icons">remove_circle</i> <span>Delete</span></a>
    <?php } endif; ?>
    <br>
    <?php 
    
    if(in_array('approve', $permissions) && $role != 'HO') { ?>
        
        <input type="button" class="btn btn-success doctorAction" data-status="approve" data-type="ajax-loader" value="Approve"/>

        <input type="button" class="btn btn-danger doctorAction" data-status="disapprove" data-type="ajax-loader" value="Disapprove"/>

    <?php } ?>
<?php echo form_close(); ?>

</div>
