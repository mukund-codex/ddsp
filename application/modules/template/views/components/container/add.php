<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2><?= ($controller == 'doctor') ? 'Doctor Day Activity' : $section_title ?></h2>
			</div>

			<div class="body">
				<?php $this->load->view("$controller/add"); ?>
			</div>
		</div>
	</div>
</div>

<?php //echo $this->load->view('template/popup/popup-box') ?>