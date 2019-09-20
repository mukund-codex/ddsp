<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record->users_id; ?>
<?php $users_type = strtolower($record->users_type); ?>
<tr>
	<td>
		<input type="checkbox" name="ids[]" value="<?php echo $id ?>" id="check_<?= $id ?>" class="chk-col-<?= $active_theme; ?> filled-in" />
		<label for="check_<?= $id ?>"></label>
	</td>
	<td><?php echo $record->users_name ?></td>
	<td><?php echo $record->users_mobile ?></td>
	<?php if($users_type == 'ho'): ?>
	<td><?php echo $record->users_password ?></td>
	<?php endif; ?>

	<?php if($users_type == 'zsm'): ?>
	<td><?php echo $record->zone_name ?></td>
	<?php endif; ?>

	<?php if($users_type == 'rsm'): ?>
	<td><?php echo $record->region_name ?></td>
	<td><?php echo $record->mgr_name ?></td>
	<td><?php echo $record->zone_name ?></td>
	<?php endif; ?>

	<?php if($users_type == 'asm'): ?>
	<td><?php echo $record->area_name ?></td>
	<td><?php echo $record->mgr_name ?></td>
	<td><?php echo $record->region_name ?></td>
	<?php endif; ?>

	<?php if($users_type == 'mr'): ?>
	<td><?php echo $record->city_name ?></td>
	<td><?php echo $record->mgr_name ?></td>
	<td><?php echo $record->area_name ?></td>
	<?php endif; ?>

	<td><p><a href="<?php echo base_url("$controller/edit/type/$users_type/record/$id?c=$timestamp") ?>" class="tooltips" title="Edit <?php ucfirst($m_title) ?>" ><i class="fa fa-edit"></i> Edit <?= ucfirst($m_title) ?></a></p></td>
</tr>
<?php $i++;  } ?>

<?php else: ?>
	<tr><td colspan="<?= (count($columns) + 2) ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
	<td colspan="<?= (count($columns) + 2) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>