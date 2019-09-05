<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title></title>
        <!-- Custom fonts for this template-->
        <link href="<?php echo base_url() ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="<?php echo base_url() ?>assets/css/custom.css" rel="stylesheet">
    </head>
 
<body class="bg-gradient-primary">
    <div class="container">
		

			<?php
                    if(!isset($module)){
                        $module = $this->uri->segment(1);
                    }

                    if(!isset($viewFile)){
                        $viewFile = $this->uri->segment(2);
                    }

                    if( $module != '' && $viewFile != '' ){
                        $path = $module. '/' . $viewFile;
                        echo $this->load->view($path);
                    }
            ?>
	</div>

	<script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/node-waves/waves.js"></script>
    <script src="<?php echo base_url() ?>assets/js/admin.js"></script>
    <script src="<?php echo base_url() ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script> 
    <script src="<?php echo base_url() ?>/assets/js/custom.min.js"></script>
</body>
</html>
