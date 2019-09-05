<!DOCTYPE html>
<html>

<head>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title><?= $pg_title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- BEGIN PLUGIN CSS -->
    <link href="<?php echo base_url() ?>front-assets/assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="<?php echo base_url() ?>front-assets/assets/plugins/bootstrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>front-assets/assets/plugins/bootstrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?php echo base_url() ?>front-assets/assets/plugins/animate.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>front-assets/assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" />
    <!-- END PLUGIN CSS -->
    <!-- BEGIN CORE CSS FRAMEWORK -->
    <link href="<?php echo base_url() ?>front-assets/webarch/css/webarch.css" rel="stylesheet" type="text/css" />
    <!-- END CORE CSS FRAMEWORK -->

    <script type="text/javascript">
        var baseUrl = "<?php echo base_url() ?>";
    </script>

</head>
 
<body class="">
    <div class="container">
        <?php
            if(!isset($module)){
                $module = $this->uri->segment(1);
            }

            if(!isset($viewFile)){
                $viewFile = $this->uri->segment(2);
            }

            if($viewFile == 'template/pages/shared-lists'){
                echo $this->load->view($viewFile);
            }
            elseif( $module != '' && $viewFile != '' ){
                $path = $module. '/' . $viewFile;
                echo $this->load->view($path);
            }
            
        ?>
    </div>

    <script src="<?php echo base_url() ?>assets/plugins/sweetalert/sweetalert2.min.js"></script>
	<script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/node-waves/waves.js"></script>
    <script src="<?php echo base_url() ?>assets/js/admin.js"></script>

    <script type="text/javascript">var controller = "<?php echo $controller ?>";</script>

    <?php if(isset($js) && sizeof($js)): foreach($js as $javascript): ?>
    <script type="text/javascript" src="<?php echo base_url()?>assets/resources/<?php echo $javascript ?>?ver=<?php echo $timestamp ?>"></script>
    <?php endforeach; endif; ?>
</body>
</html>
