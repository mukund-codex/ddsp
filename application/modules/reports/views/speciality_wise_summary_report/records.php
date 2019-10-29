<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { ?>
<tr>
    <td><?php echo $record['zsm_name'] ?></td>  
    <td><?php echo $record['zone'] ?></td>  
    <td><?php echo $record['asm_name'] ?></td>   
    <td><?php echo $record['area'] ?></td>  
    <td><?php echo !empty($record['no_of_days']) ? $record['no_of_days'] : 0; ?></td>  
    <td><?php echo !empty($record['chemist_count']) ? $record['chemist_count'] : 0; ?></td>   
    <td><?php echo !empty($record['doctor_count']) ? $record['doctor_count'] : 0; ?></td>  
    <td><?php echo !empty($record['derma']) ? $record['derma'] : 0; ?></td>   
    <td><?php echo !empty($record['cp']) ? $record['cp'] : 0; ?></td>   
    <td><?php echo !empty($record['gp']) ? $record['gp'] : 0; ?></td>   
    <td><?php echo !empty($record['gynae']) ? $record['gynae'] : 0; ?></td>   
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= count($columns)  ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= count($columns) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>