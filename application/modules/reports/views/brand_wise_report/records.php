<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { ?>
<tr>
    <td><?php echo $record['zsm_name'] ?></td>   
    <td><?php echo $record['zone'] ?></td>  
    <td><?php echo $record['asm_name'] ?></td>   
    <td><?php echo $record['area'] ?></td>   
    <td><?php echo $record['mr_name'] ?></td>   
    <td><?php echo $record['city'] ?></td>
    <td><?php echo $record['chemist_name'] ?></td>
    <td><?php echo $record['chemist_address'] ?></td>
    <td><?php echo $record['doctor_name'] ?></td>
    <td><?php echo $record['doctor_address'] ?></td>
    <td><?php echo $record['molecule_name'] ?></td>
    <td><?php echo $record['brand_name'] ?></td>   
    <td><?php echo !empty($record['rxn']) ? $record['rxn'] : 0; ?></td>   
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= count($columns)  ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= count($columns) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>