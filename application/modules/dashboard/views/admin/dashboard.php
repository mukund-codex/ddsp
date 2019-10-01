<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-blue">
                <div class="icon">
                    <i class="material-icons">devices</i>
                </div>
                <div class="content">
                    <div class="text">Total Chemist</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $chemist_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $chemist_count; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-pink hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">face</i>
                </div>
                <div class="content">
                    <div class="text">Total Doctor</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $doctor_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $doctor_count; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-cyan hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">person_add</i>
                </div>
                <div class="content">
                    <div class="text" style="margin-top:0px !important">ASM Approved Count</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $asm_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $asm_count; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box bg-light-green hover-expand-effect">
                <div class="icon">
                    <i class="material-icons">person_add</i>
                </div>
                <div class="content">
                    <div class="text" style="margin-top:0px !important">ZSM Approved Doctor</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $zsm_count; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $zsm_count; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="header">
                <h2>Zone Wise Count</h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover dashboard-task-infos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Zone</th>
                                <th>Chemist Count</th>
                                <th>Doctor Count</th>
                                <th>ASM Approved Count</th>
                                <th>ZSM Approved Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dashboard_table_data as $key => $data): ?>
                                <tr>
                                    <td><?php echo ++$key; ?></td>
                                    <td><?php echo $data['zone']; ?></td>
                                    <td><?php echo $data['chemist_count']; ?></td>
                                    <td><?php echo $data['doctor_count']; ?></td>
                                    <td><?php echo $data['asm_count']; ?></td>
                                    <td><?php echo $data['zsm_count']; ?></td>
                                </tr>    
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>