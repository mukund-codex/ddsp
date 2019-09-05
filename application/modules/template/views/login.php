<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Sign In | <?= $pg_title ?></title>
    <!-- <link rel="icon" href="../../favicon.ico" type="image/x-icon"> -->

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="manifest" href="<?php echo base_url('manifest.json') ?>">
    <link href="<?php echo base_url() ?>assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/plugins/node-waves/waves.css" rel="stylesheet" />
    <link href="<?php echo base_url() ?>assets/plugins/animate-css/animate.css" rel="stylesheet" />
    <link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-box">
		<div class="logo">
		    <a href="javascript:void(0);"><b><?= $this->config->item('title') ?></b><br><?php echo strtoupper($module) ?></a>
		</div>
		
		<div class="card">
            <div class="body">
			<?php
                    if(!isset($module)){
                        $module = $this->uri->segment(1);
                    }

                    if(!isset($viewFile)){
                        $viewFile = $this->uri->segment(2);
                    }

                    if( $module != '' && $viewFile != '' ){
                        $path = $controller . '/' . $viewFile;
                        echo $this->load->view($path);
                    }
            ?>
			</div>
		</div>
	</div>

	<script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.js"></script>
    <script src='https://www.recaptcha.net/recaptcha/api.js' async defer></script>
    <script src="<?php echo base_url() ?>assets/plugins/node-waves/waves.js"></script>
    <script src="<?php echo base_url() ?>assets/js/admin.js"></script>
    <script src="<?php echo base_url('pwabuilder-sw.js') ?>"></script>
</body>
</html>