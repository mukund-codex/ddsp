<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Doctor_generation_status extends Reports_Controller
{
	private $module = 'doctor_generation_status';
	private $controller = 'reports/doctor_generation_status';
    private $model_name = 'mdl_doctor_generation_status';
    private $columns = ['Manager Name', 'Manager Type', 'Name', 'Designation', 'EMP ID', 'Zone', 'Region', 'Area', 'City', 'Doctor', 'Mobile', 'Download Status', 'Shared Status'];
	
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
