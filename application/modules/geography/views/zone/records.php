<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record['zone_id']; ?>
<tr>
    <td>
        <input type="checkbox" name="ids[]" value="<?php echo $id ?>" id="check_<?= $id ?>" class="chk-col-<?= $settings['theme'] ?> filled-in" />
        <label for="check_<?= $id ?>"></label>
    </td>
    <td><?php echo $record['zone_name'] ?></td>   
    <td><?php echo $record['insert_dt'] ?></td>
    <td><p><a href="<?php echo base_url("$controller/edit/record/$id?c=$timestamp") ?>" class="tooltips" title="Edit <?php ucfirst($module_title) ?>" ><i class="fa fa-edit"></i> Edit <?= ucfirst($module_title) ?></a></p></td>
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= (count($columns) + 2) ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= (count($columns) + 2) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>