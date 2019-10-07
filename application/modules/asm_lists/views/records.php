
<?php $i = 1; if(sizeof($collection)) : foreach ($collection as $record) { $id = $record['doctor_id']; ?>
<tr>
    <?php  $user_role = $this->session->get_field_from_session('role','user');
     if(! isset($all_action) || $all_action || in_array('approve', $permissions) && $role != 'HO'): ?>
        <td>
            <?php if($record['zsm_status'] == 'pending'){ ?>
            <input type="checkbox" name="id[]" value="<?php echo $id ?>" id="check_<?= $id ?>" class="chk-col-<?= $settings['theme'] ?> filled-in" />
            <label for="check_<?= $id ?>"></label>
            <?php } ?>
        </td>
    <?php endif; ?>
    <?php
     if(empty($user_role)){ ?>
        <td><?php echo $record['zsm_name'] ?></td> 
        <td><?php echo $record['zone'] ?></td> 
    <?php } ?>
    <td><?php echo $record['asm_name'] ?></td> 
    <td><?php echo $record['area'] ?></td> 
    <td><?php echo $record['mr_name'] ?></td> 
    <td><?php echo $record['city'] ?></td> 
    <td><?php echo $record['chemist_count'] ?></td> 
    <td><?php echo $record['doctor_name'] ?></td>
    <td><?php echo $record['speciality'] ?></td>
    <td><?php echo $record['type']; ?></td>
    <td><a class="category_popup" doctor-id="<?php echo $id; ?>" category-id="1" style="cursor:pointer;"><?php echo $record['hyper'] ?></a></td>
    <td>
    <?php if($record['acne'] != 0) { ?>
        <a class="category_popup" doctor-id="<?php echo $id; ?>" category-id="2" style="cursor:pointer;"><?php echo $record['acne'] ?></a></td>
    <?php } else { ?>
        <?php echo $record['acne'] ?></td>
    <?php } ?>

     <?php if($record['anti'] != 0) { ?>
        <td><a class="category_popup" doctor-id="<?php echo $id; ?>" category-id="3" style="cursor:pointer;"><?php echo $record['anti'] ?></a></td>
    <?php } else { ?>
        <td><?php echo $record['anti'] ?></td>
    <?php } ?>
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
                                <!-- <img src="<?php echo base_url($value); ?>" alt="Image" style="width:50px;height:50px"> -->
                                Preview
                            </a>
                        <?php endif;?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </td> 
    <?php $user_role = $this->session->get_field_from_session('role','user');
     if($user_role != 'HO'){ ?>
    <td>
        <?php if($record['zsm_status'] == 'pending'){ ?>
            <a class="tooltips approve" title="Approve" data-status = "approve" data-id = "<?php echo $record['doctor_id'];?>" style="cursor:pointer;">Approve</a> &nbsp;&nbsp;&nbsp;&nbsp;
            <a class="tooltips approve" title="Disapprove" data-status = "disapprove" data-id = "<?php echo $record['doctor_id'];?>" style="cursor:pointer;">Disapprove</a>
        <?php }else if($record['zsm_status'] == 'approve'){ ?>
           <!--  <a class="tooltips approve" title="Disapprove" data-status = "disapprove" data-id = "<?php echo $record['doctor_id'];?>">Disapprove</a> -->
        <?php }else if($record['zsm_status'] == 'disapprove'){ ?>
            <!-- <a class="tooltips approve" title="Approve" data-status = "approve" data-id = "<?php echo $record['doctor_id'];?>">Approve</a>  -->
        <?php } ?>

    </td>
    <?php } ?>
   
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