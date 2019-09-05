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

<?php echo form_open("$controller/remove",array('id'=>'frm_delete', 'name'=>'frm_delete')); ?>
    <table class="table table-striped table-condensed">
        <thead>
            <tr>
                <?php if(! isset($all_action) || $all_action): ?>
                <th>
                    <input type="checkbox" name="" id="checkall" class="chk-col-<?= $settings['theme'] ?> filled-in">
                    <label for="checkall" style="margin:0; vertical-align:bottom"></label>
                </th>
                <?php endif ?>

                <?php foreach ($columns as $headers) { ?>
                <th class="font-bold"><?= $headers ?></th>
                <?php } ?>
                

                <?php if(! isset($all_action) || $all_action): ?>

               <?php if(in_array('edit', $permissions)) : ?>
                <th class="font-bold"><i class="fa fa-edit"></i> Action</th>
            <?php endif; ?>
            
                <?php endif; ?>
            </tr>
            
            <?php if(isset($show_filters) ? $show_filters : FALSE): ?>
            <tr>
                <?php if(! isset($all_action) || $all_action): ?>
                <td></td>
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
                
                <?php if(! isset($all_action) || $all_action): ?>
                <td></td>
                <?php endif; ?>
            </td>
            <?php endif; ?>
        </thead>
        <tbody id="tbody">
            <?php echo $this->load->view($records_view); ?>
        </tbody>
    </table>
     <?php if(isset($permissions) && count($permissions)) : if(in_array('remove', $permissions)) {  ?>
    <a class="btn btn-danger deleteAction" href="#" data-type="ajax-loader"><i class="material-icons">remove_circle</i> <span>Delete</span></a>
    <?php } endif; ?>
<?php echo form_close(); ?>
</div>