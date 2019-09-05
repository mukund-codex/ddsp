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