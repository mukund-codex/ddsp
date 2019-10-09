<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record['id']; ?>
<tr>
	<td><?php echo $record['msg_for'] ?></td>
	<td><?php echo $record['mobile'] ?></td>
	<td><?php echo $record['message'] ?></td>
	<td><?php echo $record['output'] ?></td>
	<td><?php echo $record['insertdatetime'] ?></td>
</tr>
<?php $i++;  } ?>

<?php else: ?>
	<tr><td colspan="<?= (count($columns) + 2) ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
	<td colspan="<?= (count($columns) + 2) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>