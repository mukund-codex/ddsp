<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record['doctor_id']; ?>
<tr>
    <?php if(! isset($all_action) || $all_action): ?>
        <td>
            <input type="checkbox" name="ids[]" value="<?php echo $id ?>" id="check_<?= $id ?>" class="chk-col-<?= $settings['theme'] ?> filled-in" />
            <label for="check_<?= $id ?>"></label>
        </td>
    <?php endif; ?>
    <?php $user_role = $this->session->get_field_from_session('role','user');
     if(empty($user_role)){ ?>
        <!-- <td><?php echo $record['zsm_name'] ?></td> 
        <td><?php echo $record['zone'] ?></td> 
        <td><?php echo $record['asm_name'] ?></td> 
        <td><?php echo $record['area'] ?></td> -->
    <?php } ?>
    <td><?php echo $record['asm_name'] ?></td> 
    <td><?php echo $record['area'] ?></td>
    <td><?php echo $record['mr_name'] ?></td> 
    <td><?php echo $record['city'] ?></td> 
    <td><?php echo $record['doctor_name'] ?></td>
    <td><?php echo $record['type'] ?></td>
    <td><?php echo $record['speciality'] ?></td>
    <td style="font-weight:bold;">
        <?php 
            if($record['asm_status'] == 'approve'){
                $status = "<span style='color:green'>".ucfirst($record['asm_status'])."</span>";
            }else if($record['asm_status'] == 'disapprove'){
                $status = "<span style='color:red'>".ucfirst($record['asm_status'])."</span>";
            }else{
                $status = "<span style='color:#0394fc'>".ucfirst($record['asm_status'])."</span>";
            }
            
            echo $status;
        ?>
    </td>
    <td style="font-weight:bold;">
        <?php 
            if($record['zsm_status'] == 'approve'){
                $status = "<span style='color:green'>".ucfirst($record['zsm_status'])."</span>";
            }else if($record['zsm_status'] == 'disapprove'){
                $status = "<span style='color:red'>".ucfirst($record['zsm_status'])."</span>";
            }else{
                $status = "<span style='color:#0394fc'>".ucfirst($record['zsm_status'])."</span>";
            }
            
            echo $status;
        ?>
    </td>
    <td><?php if(!empty($record['images'])): ?>
            <?php $rx_files = explode(',', $record['images']); ?>
            <?php if(count($rx_files)): ?>
                <?php foreach ($rx_files as $key => $value): ?>
                    <?php if(file_exists($value)): ?>
                        <?php $ext = pathinfo($value, PATHINFO_EXTENSION); ?>
                        <?php if(in_array($ext,['pdf','docx','doc'])): ?>
                            <a href="<?php echo base_url($value); ?>" class="fancybox" rel="rxn_group_'".$i."'" download>Document</a>
                        <?php else:?>
                            <a href="<?php echo base_url($value); ?>" class="fancybox" rel="rxn_group_'".$i."'">
                                <img src="<?php echo base_url($value); ?>" alt="Image" style="width:50px;height:50px">
                            </a>
                        <?php endif;?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </td> 
   
</tr>
<?php $i++;  } ?>

<?php else: ?>
    <tr><td colspan="<?= (count($columns) + 2) ?>"><center><i>No Record Found</i></center></td><tr>
<?php endif; ?>
<tr>
    <td colspan="<?= (count($columns) + 2) ?>"><?php echo $this->ajax_pagination->create_links(); ?></td>
</tr>
<style>
.share-btn {
    display:none;
}
@media only screen and (max-width: 1024px) {
.share-btn {
    display:block;
}
</style>