<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2><?= $section_title ?></h2>
			</div>
			
			<div class="body">
			<?php if(sizeof($info)) : ?>
				<?php $this->load->view("$controller/edit"); ?>
			<?php else: ?>
				<h4 class="mb">No Record Found !!!</h4>
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>
