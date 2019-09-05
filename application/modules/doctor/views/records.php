<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record['doctor_id']; ?>
<tr>
    <?php if(! isset($all_action) || $all_action): ?>
        <td>
            <input type="checkbox" name="ids[]" value="<?php echo $id ?>" id="check_<?= $id ?>" class="chk-col-<?= $settings['theme'] ?> filled-in" />
            <label for="check_<?= $id ?>"></label>
        </td>
    <?php endif; ?>
    <td><?php echo $record['name'] ?></td>   
    <td><?php echo $record['mobile'] ?></td>   
    <td>
        <?php if(!empty($record['poster']) && file_exists($record['poster'])): ?>
            <a href="<?php echo base_url($record['poster']) ?>" class="fancybox">Preview</a>
            <?php if(!in_array(strtoupper($role), ['SA', 'A'])): ?>
                <div>
                    <a href="#" class="share-btn" data-id="<?php echo $id ?>">
                        Share
                    </a>
                    
                    <a href = "whatsapp://send?text=<?php echo base_url($record['poster']) ?>&phone=+91<?php echo $record['mobile'] ?>" style="display: none;">Whatsapp</a>

                    <?php $file_array = explode("/", $record['poster']) ?>
                    <a href="#" class="download-btn" data-id="<?php echo $id ?>">
                        Download
                    </a>
                    <a download="<?php echo end($file_array) ?>" href="<?php echo base_url($record['poster']) ?>" style="display: none;">Download</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </td>   
    <td>
        <?php if(!empty($record['photo']) && file_exists($record['photo'])): ?>
            <a href="<?php echo base_url($record['photo']) ?>" class="fancybox">Preview</a>
        <?php endif; ?>
    </td> 
      
    <td><?php echo $record['insert_dt'] ?></td>
    <?php if(in_array('edit', $permissions)): ?>
        <td><p><a href="<?php echo base_url("$controller/edit/record/$id?c=$timestamp") ?>" class="tooltips" title="Edit <?php ucfirst($module_title) ?>" ><i class="fa fa-edit"></i> Edit <?= ucfirst($module_title) ?></a></p></td>
    <?php endif; ?>
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= (count($columns) + 2) ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= (count($columns) + 2) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>
<style>
.share-btn {
    display:none;
}
@media only screen and (max-width: 1024px) {
.share-btn {
    display:block;
}
</style>