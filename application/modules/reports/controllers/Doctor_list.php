<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Doctor_list extends Reports_Controller
{
	private $module = 'doctor_list';
	private $controller = 'reports/doctor_list';
    private $model_name = 'mdl_doctor_list';
    private $columns = ['ZSM', 'Zone', 'ASM', 'Area', 'MR Name', 'HQ', 'Doctor Name', 'Type', 'Speciality', 'ABM Status', 'ZBM Status', 'Images', 'Date'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
