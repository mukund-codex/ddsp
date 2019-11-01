
<?php if($module_title == 'asm_lists'){ ?>
    <div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box-4">
                <div class="icon">
                    <i class="material-icons col-blue">devices</i>
                </div>
                <div class="content">
                    <div class="text">Total</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $total; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $total; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box-4">
                <div class="icon">
                    <i class="material-icons col-teal">equalizer</i>
                </div>
                <div class="content">
                    <div class="text">Derma</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $derma; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $derma; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box-4">
                <div class="icon">
                    <i class="material-icons col-teal">equalizer</i>
                </div>
                <div class="content">
                    <div class="text">CP</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $cp; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $cp; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box-4">
                <div class="icon">
                    <i class="material-icons col-teal">equalizer</i>
                </div>
                <div class="content">
                    <div class="text">GP</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $gp; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $gp; ?></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="info-box-4">
                <div class="icon">
                    <i class="material-icons col-teal">equalizer</i>
                </div>
                <div class="content">
                    <div class="text">Gynae</div>
                    <div class="number count-to" data-from="0" data-to="<?php echo $gynae; ?>" data-speed="1000" data-fresh-interval="20"><?php echo $gynae; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php  } ?>  
<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header" style="position:relative">
    			<h2><?= (str_word_count($module_title) === 1) ? 'Manage ' . ucfirst($module_title) : ucwords(str_replace('_', ' ',$module_title)) ?></h2>

                <?php if(isset($permissions) && count($permissions)) : ?>
                    <?php echo $this->load->view('template/components/action-btns'); ?>
                <?php endif; ?>

                <?php if($controller == 'doctor' && in_array('add', $permissions)): ?>
                <a href="<?php echo base_url('doctor/add') ?>" class="btn btn-primary" style="position:absolute; right:18px; top:15px">Add Doctor</a>
                <?php endif; ?>
			</div>
            
			<?php echo $this->load->view('template/components/table-listing') ?>
		</div>
	</div>
</div>
<!-- #END# Basic Table -->
<?php echo $this->load->view('template/popup/csv-popup-box') ?>

<script type="text/javascript">
	var listing_url = "<?php echo $listing_url ?>";
	var download_url = "<?php echo $download_url ?>";

</script>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Data</h4>
            </div>
            <div class="modal-body" style="display: block;width: auto;max-height: 100%">
                <img id="image_modal" style="width: 100%" src="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>