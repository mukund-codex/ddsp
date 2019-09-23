<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { ?>
<tr>
    <td><?php echo $record['mr_name'] ?></td>   
    <td><?php echo $record['asm_name'] ?></td>  
    <td><?php echo $record['zsm_name'] ?></td>   
    <td><?php echo $record['login_time'] ?></td>   
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= count($columns)  ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= count($columns) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>