<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-blue">
                <div class="icon">
                    <i class="material-icons">devices</i>
                </div>
                <div class="content">
                    <div class="text">Chemist Count</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $chemist_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $chemist_count; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-indigo">
                <div class="icon">
                    <i class="material-icons">face</i>
                </div>
                <div class="content">
                    <div class="text">Doctor Count</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $doctor_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $doctor_count; ?></div>
                </div>
            </div>
        </div>
        <?php $role = $this->session->get_field_from_session('role', 'user');
		if($role != 'HO'){ ?>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-indigo">
                <div class="icon">
                    <i class="material-icons">assignment_turned_in</i>
                </div>
                <div class="content">
                    <div class="text">Approved Doctor Count</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $approved_doctor_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $approved_doctor_count; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-indigo">
                <div class="icon">
                    <i class="material-icons">assignment_late</i>
                </div>
                <div class="content">
                    <div class="text">Disapproved Doctor Count</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $disapproved_doctor_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $disapproved_doctor_count; ?></div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>