<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Zone_wise_doctor extends Reports_Controller
{
	private $module = 'zone_wise_doctor';
	private $controller = 'reports/zone_wise_doctor';
    private $model_name = 'mdl_zone_wise_doctor';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'Chemist Count', 'Doctor Count', 'ABM Approved Count', 'ZBM Approved Count'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
