<ul class="header-dropdown m-r--5">
    <li class="dropdown">
        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="material-icons">more_vert</i>
        </a>
        <ul class="dropdown-menu pull-right">
            <?php if(in_array('add', $permissions)) : ?>
            <li><a href="<?php echo base_url("$controller/add") ?>">Add <?= ucfirst($module_title) ?></a></li>
            <?php endif; ?>

            <?php if(in_array('upload', $permissions)) : ?>
            <li><a href="#" id="import" data-toggle="modal" data-target="#uploadbox" title="Upload CSV">Upload CSV</a></li>
            <?php endif; ?>

            <?php if(in_array('download', $permissions)) : ?>
            <li><a href="<?php echo base_url("$download_url") ?>" id="export" title="Export">Export</a></li>
            <?php endif; ?>
            
        </ul>
    </li>
</ul>