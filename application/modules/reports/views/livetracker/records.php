<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { ?>
<tr>
    <td><?php echo $record['mgr_name'] ?></td>   
    <td><?php echo $record['mgr_type'] ?></td>  
    <td><?php echo $record['users_name'] ?></td>   
    <td><?php echo $record['users_type'] ?></td>   
    <td><?php echo $record['users_emp_id'] ?></td>   
    <td><?php echo $record['zone_name'] ?></td>   
    <td><?php echo $record['region_name'] ?></td>   
    <td><?php echo $record['area_name'] ?></td>   
    <td><?php echo $record['city_name'] ?></td>   

    <td><?php echo $record['doctor'] ?></td>   
    <td><?php echo $record['mobile'] ?></td>   
    <td><?php echo $record['poster_status'] ?></td>   
    <td><?php echo $record['message'] ?></td>
    <td><?php echo $record['insert_dt'] ?></td>
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= count($columns)  ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= count($columns) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>