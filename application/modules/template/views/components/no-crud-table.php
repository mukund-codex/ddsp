<div class="body table-responsive">
<?php echo form_open("$controller/remove",array('id'=>'frm_delete', 'name'=>'frm_delete')); ?>
    <table class="table table-striped table-condensed table-bordered">
        <thead>
            <tr>
                <?php foreach ($columns as $headers) { ?>
                <th class="font-bold"><?= $headers ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody id="tbody">
            <?php echo $this->load->view($records_view); ?>
        </tbody>
    </table>
    <!-- <a class="btn btn-danger deleteAction" href="#" data-type="ajax-loader"><i class="material-icons">remove_circle</i> <span>Delete</span></a> -->
<?php echo form_close(); ?>
</div>