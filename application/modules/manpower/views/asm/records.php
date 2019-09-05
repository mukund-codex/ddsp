<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record['users_id']; ?>
<tr>
    <td>
        <input type="checkbox" name="ids[]" value="<?php echo $id ?>" id="check_<?= $id ?>" class="chk-col-<?= $settings['theme'] ?> filled-in" />
        <label for="check_<?= $id ?>"></label>
    </td>
    <td><?php echo $record['users_name'] ?></td>   
    <td><?php echo $record['users_mobile'] ?></td>   
    <td><?php echo $record['users_emp_id'] ?></td>   
    <td><?php echo $record['users_password'] ?></td>   
    <td><?php echo $record['area_name'] ?></td>   
    <td><?php echo $record['mgr_name'] ?></td>   
    <td><?php echo $record['region_name'] ?></td>   
    <td><?php echo $record['insert_dt'] ?></td>
    <td><p><a href="<?php echo base_url("$controller/edit/record/$id?c=$timestamp") ?>" class="tooltips" title="Edit <?php ucfirst($module_title) ?>" ><i class="fa fa-edit"></i> Edit <?= strtoupper($module_title) ?></a></p></td>
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= (count($columns) + 2) ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= (count($columns) + 2) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>