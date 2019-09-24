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
                        <!-- <li <?php echo (isset($menu) && $menu == 'region') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/region/lists?t=$timestamp") ?>">Region</a>
                        </li> -->
                        <li <?php echo (isset($menu) && $menu == 'area') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/area/lists?t=$timestamp") ?>">Area</a>
                        </li>
                        <li <?php echo (isset($menu) && $menu == 'city') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("geography/city/lists?t=$timestamp") ?>">City</a>
                        </li>
                    </ul>
                </li>
                
                <li <?php echo (in_array($menu, ['nsm', 'ho','zsm', 'rsm', 'asm', 'mr'])) ? 'class="active"': ''; ?>>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">person_add</i>
                        <span>Manpower</span>
                    </a>
                    <ul class="ml-menu">       
                        <li <?php echo (isset($menu) && $menu == 'ho') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/ho/lists?t=$timestamp") ?>">HO</a>
                        </li>     
                        <li <?php echo (isset($menu) && $menu == 'nsm') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/nsm/lists?t=$timestamp") ?>">NSM</a>
                        </li>              
                        <li <?php echo (isset($menu) && $menu == 'zsm') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/zsm/lists?t=$timestamp") ?>">ZSM</a>
                        </li>                            
                        <!-- <li <?php echo (isset($menu) && $menu == 'rsm') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/rsm/lists?t=$timestamp") ?>">RSM</a>
                        </li>   -->                          
                        <li <?php echo (isset($menu) && $menu == 'asm') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/asm/lists?t=$timestamp") ?>">ASM</a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'mr') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("manpower/mr/lists?t=$timestamp") ?>">MR</a>
                        </li>                            
                    </ul>
                </li>

                <li <?php echo (in_array($menu, ['speciality', 'speciality_category', 'category', 'molecule', 'brand', 'sku','about','state','city'])) ? 'class="active"': ''; ?>>
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">person_add</i>
                        <span>Masters</span>
                    </a>
                    <ul class="ml-menu">                 
                        <li <?php echo (isset($menu) && $menu == 'speciality') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("speciality/lists?t=$timestamp") ?>"><i class="material-icons">language</i><span>Speciality</span></a>
                        </li>
                        <li <?php echo ($mainmenu == 'speciality_category') ? 'class="active"': ''; ?> >
                            <a href="<?php echo base_url("speciality_category/lists?t=$timestamp") ?>">
                                <i class="material-icons">assignment_ind</i>
                                <span>Speciality Category</span>
                            </a>
                        </li> 
                        <li <?php echo ($mainmenu == 'category') ? 'class="active"': ''; ?> >
                            <a href="<?php echo base_url("category/lists?t=$timestamp") ?>">
                                <i class="material-icons">assignment_ind</i>
                                <span>Category</span>
                            </a>
                        </li>               
                        <li <?php echo (isset($menu) && $menu == 'molecule') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("molecule/lists?t=$timestamp") ?>"><i class="material-icons">assignment_ind</i><span>Molecules</span></a>
                        </li>                            
                        <li <?php echo (isset($menu) && $menu == 'brand') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("brand/lists?t=$timestamp") ?>"><i class="material-icons">assignment_ind</i><span>Brands</span></a>
                        </li>                            
                        <!-- <li <?php echo (isset($menu) && $menu == 'sku') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("sku/lists?t=$timestamp") ?>"><i class="material-icons">assignment_ind</i><span>SKU</span></a>
                        </li>  -->  
                        <li <?php echo (isset($menu) && $menu == 'about') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("about/lists?t=$timestamp") ?>"><i class="material-icons">assignment_ind</i><span>About</span></a>
                        </li> 
                        <li <?php echo (isset($menu) && $menu == 'state') ? 'class="active"': ''; ?> style="display:none;">
                            <a href="<?php echo base_url("state/lists?t=$timestamp") ?>"><i class="material-icons">assignment_ind</i><span>State Master</span></a>
                        </li>    
                        <li <?php echo (isset($menu) && $menu == 'city') ? 'class="active"': ''; ?> style="display:none;">
                            <a href="<?php echo base_url("city/lists?t=$timestamp") ?>"><i class="material-icons">assignment_ind</i><span>City Master</span></a>
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

                <li <?php echo ($mainmenu == 'speciality') ? 'class="active"': ''; ?> style="display:none;">
                    <a href="<?php echo base_url("speciality/lists?t=$timestamp") ?>">
                        <i class="material-icons">language</i>
                        <span>Speciality</span>
                    </a>
                </li>
                
                <li <?php echo ($mainmenu == 'molecule') ? 'class="active"': ''; ?> style="display:none;">
                    <a href="<?php echo base_url("molecule/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>Molecules</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'brand') ? 'class="active"': ''; ?> style="display:none;">
                    <a href="<?php echo base_url("brand/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>Brands</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'sku') ? 'class="active"': ''; ?> style="display:none;">
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

                <li <?php echo ($mainmenu == 'about') ? 'class="active"': ''; ?> style="display:none;">
                    <a href="<?php echo base_url("about/lists?t=$timestamp") ?>">
                        <i class="material-icons">assignment_ind</i>
                        <span>About</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'asm_lists') ? 'class="active"': ''; ?> style="display:block;">
                    <a href="<?php echo base_url("asm_lists/lists?t=$timestamp") ?>">
                        <i class="material-icons">receipt</i>
                        <span>MR Lists</span>
                    </a>
                </li>

                <li <?php echo ($mainmenu == 'zsm_lists') ? 'class="active"': ''; ?> style="display:block;">
                    <a href="<?php echo base_url("zsm_lists/lists?t=$timestamp") ?>">
                        <i class="material-icons">receipt</i>
                        <span>ASM Lists</span>
                    </a>
                </li>


                <li <?php echo ($mainmenu == 'language') ? 'class="active"': ''; ?> style="display:none;">
                    <a href="<?php echo base_url("language/lists?t=$timestamp") ?>">
                        <i class="material-icons">receipt</i>
                        <span>ZSM Lists</span>
                    </a>
                </li>

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
                    
                <li <?php echo (in_array($menu, ['login_reports', 'category_wise_report', 'molecule_wise_report', 'brand_wise_report', 'derma_dr_report', 'cp_dr_report', 'gp_dr_report', 'gynaec_dr_report'])) ? 'class="active"': ''; ?> >
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">receipt</i>
                        <span>Reports</span>
                    </a>
                    <ul class="ml-menu">    
                        <li <?php echo (isset($menu) && $menu == 'login_reports') ? 'class="active"': ''; ?>>
                            <a href="<?php echo base_url("reports/login_reports?t=$timestamp") ?>">Login Report</a>
                        </li>       
                        <li <?php echo (isset($menu) && $menu == 'category_wise_report') ? 'class="active"': ''; ?>>
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