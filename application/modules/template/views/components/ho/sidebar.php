<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
   <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url() ?>ho/home">
      <div class="sidebar-brand-icon rotate-n-15"> </div>
      <div class="sidebar-brand-text mx-3"><?php echo config_item('title') ?></div>
   </a>
   <hr class="sidebar-divider my-0">
   <li class="nav-item <?php echo ($mainmenu == 'dashboard') ? "active": ''; ?>"> <a class="nav-link" href="<?php echo base_url() ?>ho/home"> <i class="fas fa-fw fa-tachometer-alt"></i> <span>Dashboard</span></a> </li>
   <hr class="sidebar-divider">
   <li class="nav-item <?php echo ($menu == 'planning_report') ? "active": ''; ?>"> <a class="nav-link" href="<?php echo base_url() ?>"> <i class="fas fa-fw fa-chart-area"></i> <span>Planning Report</span></a></li>
   <hr class="sidebar-divider d-none d-md-block">
   <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
   </div>
</ul>
