<div class="container-fluid">

    <div class="row">
        <div class="form-group">
            <div class="form-group">
            <select name="zone_id" id="zone_id" data-placeholder="Select Zone" class="form-control">
                <option value="">Select Zone</option>
            </select>
            </div>
        </div>

        <div class="form-group">
            <div class="form-group">
                <select name="region_id" id="region_id" data-placeholder="Select Region" class="form-control">
                    <option value="">Select Region</option>
                </select>
            </div>
        </div>
    </div>

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
    </div>
</div>