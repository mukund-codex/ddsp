<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { ?>
<tr>
    <td><?php echo $record['zsm_name'] ?></td>  
    <td><?php echo $record['zone'] ?></td>  
    <td><?php echo $record['asm_name'] ?></td>   
    <td><?php echo $record['area'] ?></td>  
    <td><?php echo $record['chemist_count'] ?></td>  
    <td><?php echo $record['total_reps'] ?></td>  
    <td><?php echo $record['no_of_days'] ?></td>  
    <td><?php echo $record['chemist_avg'] ?></td>   
    <td><?php echo $record['doctor_count'] ?></td>  
    <td><?php echo $record['asm_count'] ?></td>   
    <td><?php echo $record['zsm_count'] ?></td>   
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= count($columns)  ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= count($columns) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>