<!-- #Top Bar -->
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
        <div class="user-info">
            <div class="image" style="text-align:center">
            </div>
            <div class="info-container">
                <div><?php echo $this->session->get_field_from_session('user_name','user'); ?></div>

                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="color:<?= $settings['theme'] ?>">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:void(0);"><i class="material-icons">person</i><?php echo $this->session->get_field_from_session('user_name','user'); ?></a></li>
                        <li role="seperator" class="divider"></li>
                        <li><a href="<?php echo base_url() ?>dashboard/user/logout"><i class="material-icons">input</i>Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                <li <?php echo ($menu == 'user') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url('dashboard/user') ?>">
                        <i class="material-icons">home</i>
                        <span>Home</span>
                    </a>
                </li>
                <?php if(in_array($user_role, ['ASM'])): ?>
                    <li <?php echo ($mainmenu == 'mr_lists') ? 'class="active"': ''; ?> style="display:block;">
                        <a href="<?php echo base_url("mr_lists/lists?t=$timestamp") ?>">
                            <i class="material-icons">receipt</i>
                            <span>MR Lists</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(in_array($user_role, ['ZSM'])): ?>
                    <li <?php echo ($mainmenu == 'asm_lists') ? 'class="active"': ''; ?> style="display:block;">
                        <a href="<?php echo base_url("asm_lists/lists?t=$timestamp") ?>">
                            <i class="material-icons">receipt</i>
                            <span>ASM Lists</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(in_array($user_role, ['RSM','ASM','MR'])): ?>
                    <!-- <li <?php echo ($menu == 'doctor') ? 'class="active"': ''; ?>>
                        <a href="<?php echo base_url("doctor/lists?t=$timestamp") ?>">
                            <i class="material-icons">person_add</i>
                            <span>Doctors</span>
                        </a>
                    </li> -->
                <?php endif; ?>

                <?php if($user_role == 'HO'): ?>
                    <li <?php echo ($mainmenu == 'communication') ? 'class="active"': ''; ?>>
                        <a href="<?php echo base_url("communication/lists?t=$timestamp") ?>">
                            <i class="material-icons">assignment_ind</i>
                            <span>Communication</span>
                        </a>
                    </li>
                    <li <?php echo ($mainmenu == 'asm_lists') ? 'class="active"': ''; ?> style="display:block;">
                        <a href="<?php echo base_url("asm_lists/lists?t=$timestamp") ?>">
                            <i class="material-icons">receipt</i>
                            <span>ASM Lists</span>
                        </a>
                    </li>
                    <li <?php echo (in_array($menu, ['login_reports', 'category_wise_report', 'molecule_wise_report', 'brand_wise_report', 'derma_dr_report', 'cp_dr_report', 'gp_dr_report', 'gynaec_dr_report', 'zone_wise_doctor','chemist_list'])) ? 'class="active"': ''; ?> >
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">receipt</i>
                        <span>Reports</span>
                    </a>
                    <ul class="ml-menu">    
                       <!--  <li <?php echo (isset($menu) && $menu == 'login_reports') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/login_reports?t=$timestamp") ?>">Login Report</a>
                        </li>   -->
                        <li <?php echo (isset($menu) && $menu == 'chemist_list') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/chemist_list?t=$timestamp") ?>">Chemist List</a>
                        </li> 
                        <li <?php echo (isset($menu) && $menu == 'zone_wise_doctor') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/zone_wise_doctor?t=$timestamp") ?>">Zone Wise Doctor</a>
                        </li>      
                       <!--  <li <?php echo (isset($menu) && $menu == 'category_wise_report') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/category_wise_report?t=$timestamp") ?>">Category Wise Report</a>
                        </li>  
                        <li <?php echo (isset($menu) && $menu == 'molecule_wise_report') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/molecule_wise_report?t=$timestamp") ?>">Molecule Wise Report</a>
                        </li>    
                        <li <?php echo (isset($menu) && $menu == 'brand_wise_report') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/brand_wise_report?t=$timestamp") ?>">Brand Wise Report</a>
                        </li>     
                        <li <?php echo (isset($menu) && $menu == 'derma_dr_report') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/derma_dr_report?t=$timestamp") ?>">Derma Dr Live Report</a>
                        </li>  
                        <li <?php echo (isset($menu) && $menu == 'cp_dr_report') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/cp_dr_report?t=$timestamp") ?>">CP Dr Live Report</a>
                        </li>
                        <li <?php echo (isset($menu) && $menu == 'gp_dr_report') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/gp_dr_report?t=$timestamp") ?>">GP Dr Live Report</a>
                        </li>
                        <li <?php echo (isset($menu) && $menu == 'gynaec_dr_report') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/gynaec_dr_report?t=$timestamp") ?>">Gynaec Dr Live Report</a>
                        </li>  -->              
                    </ul>
                </li>
                   
                <?php endif; ?>

            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; <?= (date('Y') - 1) ?> - <?= date('Y') ?> <a href="javascript:void(0);"><?php echo $user_role ?> - <?= config_item('title') ?></a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0
            </div>
        </div>
        <!-- #Footer -->
    </aside>

</section>