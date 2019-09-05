<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="<?= base_url('dashboard/user') ?>"><?= (!empty($settings['app_name'])) ? $settings['app_name'] : config_item('title') . ''; ?></a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo base_url() ?>dashboard/user/logout"><i class="material-icons">power_settings_new</i></a></li>
            <li><a style="cursor:default; margin-top:19px"><?php echo ucwords($this->session->get_field_from_session('user_name', 'user')); ?></a></li>
            </ul>
        </div>
    </div>
</nav>
