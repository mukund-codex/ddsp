<!-- #Top Bar -->
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN NAVIGATION</li>
                
                <li <?php echo ($mainmenu == 'dashboard') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url('dashboard/admin') ?>">
                        <i class="material-icons">home</i>
                        <span>Home</span>
                    </a>
                </li>

                <?php if($role == 'SA'): ?>
                    <li <?php echo ($mainmenu == 'admin') ? 'class="active"': ''; ?>>
                        <a href="<?php echo base_url("admin/lists?t=$timestamp") ?>">
                            <i class="material-icons">assignment_ind</i>
                            <span>Admins</span>
                        </a>
                    </li>

                    <li <?php echo (in_array($menu, ['basic'])) ? 'class="active"': ''; ?>>
                        <a href="javascript:void(0);" class="menu-toggle">
                            <i class="material-icons">settings_applications</i>
                            <span>Settings</span>
                        </a>
                        <ul class="ml-menu">
                            <li <?php echo (isset($menu) && $menu == 'basic') ? 'class="active"': ''; ?>>
                                <a href="<?php echo base_url("settings/basic?t=$timestamp") ?>">Basic Settings</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li <?php echo (in_array($menu, ['national_zone','zone', 'region', 'area', 'city'])) ? 'class="active"': ''; ?>>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">language</i>
                        <span>Geography</span>
                    </a>
                    <ul class="ml-menu">     
                        <li <?php echo (isset($menu) && $menu == 'national_zone') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/national_zone/lists?t=$timestamp") ?>">National Zone</a>
                        </li>                     
                        <li <?php echo (isset($menu) && $menu == 'zone') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/zone/lists?t=$timestamp") ?>">Zone</a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'region') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/region/lists?t=$timestamp") ?>">Region</a>
                        </li>
                        <li <?php echo (isset($menu) && $menu == 'area') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/area/lists?t=$timestamp") ?>">Area</a>
                        </li>
                        <li <?php echo (isset($menu) && $menu == 'city') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/city/lists?t=$timestamp") ?>">City</a>
                        </li>
                    </ul>
                </li>
                
                <li <?php echo (in_array($menu, ['ho','zsm', 'rsm', 'asm', 'mr'])) ? 'class="active"': ''; ?>>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">person_add</i>
                        <span>Manpower</span>
                    </a>
                    <ul class="ml-menu">       
                        <li <?php echo (isset($menu) && $menu == 'ho') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/ho/lists?t=$timestamp") ?>">HO</a>
                        </li>                 
                        <li <?php echo (isset($menu) && $menu == 'zsm') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/zsm/lists?t=$timestamp") ?>">ZSM</a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'rsm') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/rsm/lists?t=$timestamp") ?>">RSM</a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'asm') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/asm/lists?t=$timestamp") ?>">ASM</a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'mr') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/mr/lists?t=$timestamp") ?>">MR</a>
                        </li>                            
                    </ul>
                </li>

                <!-- <li <?php echo ($mainmenu == 'doctor') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("doctor/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>Doctor</span>
                    </a>
                </li> -->

                <li <?php echo ($mainmenu == 'communication') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("communication/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>Communication</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'speciality') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("speciality/lists?t=$timestamp") ?>">
                        <i class="material-icons">language</i>
                        <span>Speciality</span>
                    </a>
                </li>
                
                <li <?php echo ($mainmenu == 'molecule') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("molecule/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>Molecules</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'brand') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("brand/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>Brands</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'sku') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("sku/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>SKU</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'rxn') ? 'class="active"': ''; ?> style="display:none;">
                    <a href="<?php echo base_url("rxn/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>RXN/Week</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'about') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("about/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>About</span>
                    </a>
                </li>

                <!-- <li <?php echo ($mainmenu == 'language') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("language/lists?t=$timestamp") ?>">
                        <i class="material-icons">language</i>
                        <span>Language</span>
                    </a>
                </li> -->

               <!--  <li <?php echo ($mainmenu == 'bunch') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("bunch/lists?t=$timestamp") ?>">
                        <i class="material-icons">receipt</i>
                        <span>Bunch</span>
                    </a>
                </li> -->

                <!-- <li <?php echo ($mainmenu == 'coupon') ? 'class="active"': ''; ?>>
                    <a href="<?php echo base_url("coupon/lists?t=$timestamp") ?>">
                        <i class="material-icons">receipt</i>
                        <span>Coupon</span>
                    </a>
                </li> -->
                    
                <li <?php echo (in_array($menu, ['livetracker','doctor_generation_status', 'employee_ds'])) ? 'class="active"': ''; ?>>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">receipt</i>
                        <span>Reports</span>
                    </a>
                    <ul class="ml-menu">                        
                        <li <?php echo (isset($menu) && $menu == 'livetracker') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/livetracker?t=$timestamp") ?>">Livetracker</a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'employee_ds') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/employee_ds?t=$timestamp") ?>">Employee Wise Report</a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'doctor_generation_status') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/doctor_generation_status?t=$timestamp") ?>">Doctor Wise Report</a>
                        </li>   
                        <li <?php echo (isset($menu) && $menu == 'region_wise') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/region_wise?t=$timestamp") ?>">Region Wise Report</a>
                        </li>                         
                    </ul>
                </li>
            </ul>
        </div>
        <!-- #Menu -->
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
                &copy; 
                <?= (date('Y') - 1) ?> - <?= date('Y') ?>
                <a href="javascript:void(0);">
                    <?php echo $this->session->get_field_from_session('role_label') ?> Panel - <?= config_item('title') ?></a>.
            </div>
            <div class="version">
                <b>Version: </b> 1.0
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
</section>