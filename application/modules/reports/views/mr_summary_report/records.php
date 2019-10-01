<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { ?>
<tr>
    <td><?php echo $record['zsm_name'] ?></td>  
    <td><?php echo $record['zone'] ?></td>  
    <td><?php echo $record['asm_name'] ?></td>   
    <td><?php echo $record['area'] ?></td>  
    <td><?php echo $record['mr_name'] ?></td>  
    <td><?php echo $record['city'] ?></td>  
    <td><?php echo !empty($record['chemist_count']) ? $record['chemist_count'] : 0; ?></td>  
    <td><?php echo !empty($record['total_reps']) ? $record['total_reps'] : 0; ?></td>  
    <td><?php echo !empty($record['no_of_days']) ? $record['no_of_days'] : 0; ?></td>  
    <td><?php echo !empty($record['chemist_avg']) ? $record['chemist_avg'] : 0; ?></td>   
    <td><?php echo !empty($record['doctor_count']) ? $record['doctor_count'] : 0; ?></td>  
    <td><?php echo !empty($record['asm_count']) ? $record['asm_count'] : 0; ?></td>   
    <td><?php echo !empty($record['zsm_count']) ? $record['zsm_count'] : 0; ?></td>   
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= count($columns)  ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= count($columns) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>