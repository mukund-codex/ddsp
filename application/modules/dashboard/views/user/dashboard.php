<div class="container-fluid">
    <div class="block-header">
        <h2>DASHBOARD</h2>
    </div>

<?php $role = $this->session->get_field_from_session('role', 'user'); ?>
<?php if(in_array(strtoupper($role), ['MR','ASM','RSM','HO'])): ?>
    <!-- Widgets -->
<a href="<?= base_url("doctor/lists") ?>">
    <div class="row clearfix">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-cyan hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">person_add</i>
                </div>
                <div class="content">
                    <div class="text">TOTAL DOCTORS</div>
                    <div class="number count-to" data-from="0" data-to="<?= $doctor_count; ?>" data-speed="15" data-fresh-interval="10"></div>
                </div>
            </div>
        </div>
    </div>
</a>
<?php endif; ?>
<?php if(in_array(strtoupper($role), ['MR','ASM','RSM'])): ?>
    <!-- Widgets -->
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Doctor Day Activity</h2>
                </div>

                <div class="body">
                    <?php echo $this->load->view('doctor/add'); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>
