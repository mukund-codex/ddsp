<div class="block-header">
	<h2>SMS USAGE</h2>
</div>
<div class="row clearfix">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="info-box hover-zoom-effect">
			<div class="icon bg-deep-orange">
				<i class="material-icons">email</i>
			</div>
			<div class="content">
				<div class="text">TOTAL SMS</div>
				<div class="number"><?= number_format($total_balance[0]->balance); ?></div>
			</div>
		</div>

	</div>
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="info-box hover-zoom-effect">
			<div class="icon bg-blue">
				<i class="material-icons">email</i>
			</div>
			<div class="content">
				<div class="text">SMS REMAINING</div>
				<div class="number"><?= number_format($sms_balance); ?></div>
			</div>
		</div>
	</div>

	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
		<div class="info-box hover-expand-effect">
			<div class="icon bg-teal">
				<i class="material-icons">equalizer</i>
			</div>
			<div class="content">
				<div class="text">SMS USAGE</div>
				<div class="number"><?= number_format($total_balance[0]->balance - $sms_balance) ?></div>
			</div>
		</div>
	</div>
</div>

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2>
					ACTION
				</h2>
				<button type="button" class="header-dropdown m-r--5 btn btn-primary waves-effect clear-all">SHOW ALL</button>
			</div>
			<div class="body">
				<div class="row clearfix">
					<div class="col-md-2">
						<label for="from_date">Select Start Date: </label>
					</div>
					<div class="col-md-4">
						<input type="text" name="from_date" id="from_date" class="form-control" readonly="readonly" value="" autocomplete="off">
					</div>
					
					<div class="col-md-2">
						<label for="to_date">Select End Date: </label>
					</div>
					<div class="col-md-4">
						<input type="text" name="to_date" id="to_date" class="form-control" readonly="readonly" value="" autocomplete="off">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row clearfix">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="card">
			<div class="header">
				<h2>
					<?= $pg_title ?>
				</h2>
				<ul class="header-dropdown m-r--5">
					<li class="dropdown">
						<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							<i class="material-icons">more_vert</i>
						</a>
						<ul class="dropdown-menu pull-right">
							<li><a href="<?php echo base_url("$download_url") ?>" id="export" title="Export">Export</a></li>
							<li><a href="<?php echo base_url('sms/addSmsBalance') ?>" id="add_sms_balance" title="Add SMS Balance">Add SMS Balance</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="body table-responsive">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<?php foreach ($columns as $headers) { ?>
							<th class="font-bold"><?= $headers ?></th>
							<?php } ?>
						</tr>
					</thead>
					<tbody id="tbody">
						<?php include_once 'results.php'; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- #END# Basic Table -->

<script type="text/javascript">
	var listing_url = "<?php echo $listing_url ?>";
	var download_url = "<?php echo $download_url ?>";
</script>