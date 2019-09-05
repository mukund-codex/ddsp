<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="<?= $this->security->get_csrf_hash(); ?>">
    <title><?= $pg_title ?></title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <?php if( isset($plugins) && in_array('bootstrap-select', $plugins )) : ?>
        <link href="<?php echo base_url() ?>assets/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
    <?php endif; ?>

    <link href="<?php echo base_url() ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/css/custom.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/datepicker.css">
    <script type="text/javascript">
        var baseUrl = "<?php echo base_url() ?>";
    </script>
    <style>
        div#preloader {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 999999999999999;
            width: 100%;
            height: 100%;
            overflow: visible;
            background: rgba(255, 255, 255, 0.81) url("<?php echo base_url(); ?>assets/images/loader.gif") no-repeat center center; 
        }
    </style>
</head>
<body id="page-top">
    <div id="preloader"></div>
    <div id="wrapper">
        <?php //echo $this->load->view('components/loader'); ?>

        <div class="overlay"></div>        
        <?php echo $this->load->view('components/ho/sidebar'); ?>        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php echo $this->load->view('components/ho/topbar'); ?>
                <div class="container-fluid">
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
            </div>
        </div>
    </div>
    <script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
    
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.js"></script>

    <?php if( isset($plugins) && in_array('bootstrap-select', $plugins )) : ?>
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap-select/js/bootstrap-select.js"></script>
    <?php endif; ?>

    <script src="<?php echo base_url() ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="<?php echo base_url() ?>assets/js/demo.js"></script>
    <script src="<?php echo base_url() ?>assets/resources/common.js"></script>
    <script type="text/javascript">var controller = "<?php echo $controller ?>";</script>
    <?php if(ENVIRONMENT == 'production'): ?>
        <script src="<?php echo base_url() ?>assets/js/script.js"></script>
    <?php endif; ?>
    <?php if( isset($plugins) && in_array('paginate', $plugins )) : ?>
    <script src="<?php echo base_url() ?>assets/resources/paginate.js"></script>
    <?php endif;?>

    <?php if( isset($plugins) && in_array('amcharts4', $plugins )) : ?>
        <script src="<?php echo base_url(); ?>assets/plugins/amcharts4/core.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/amcharts4/charts.js"></script>
        <script src="<?php echo base_url(); ?>assets/plugins/amcharts4/themes/animated.js"></script>
    <?php endif; ?>

    <?php if(isset($js) && sizeof($js)): foreach($js as $javascript): ?>
    <script type="text/javascript" src="<?php echo base_url()?>assets/resources/<?php echo $javascript ?>?ver=<?php echo $timestamp ?>"></script>
    <?php endforeach; endif; ?>
    <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
</body>
</html>