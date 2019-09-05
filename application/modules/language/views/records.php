<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record['id']; ?>
<tr>
    <?php if(! isset($all_action) || $all_action): ?>
        <td>
            <input type="checkbox" name="ids[]" value="<?php echo $id ?>" id="check_<?= $id ?>" class="chk-col-<?= $settings['theme'] ?> filled-in" />
            <label for="check_<?= $id ?>"></label>
        </td>
    <?php endif; ?>
    <td><?php echo $record['name'] ?></td>   
    <td><?php echo $record['code'] ?></td>         
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