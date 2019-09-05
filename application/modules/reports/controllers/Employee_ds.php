<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Employee_ds extends Reports_Controller
{
	private $module = 'employee_ds';
	private $controller = 'reports/employee_ds';
    private $model_name = 'mdl_employee_ds';
    private $columns = ['Manager Name', 'Manager Type', 'Name', 'Designation', 'EMP ID', 'Zone', 'Region', 'Area', 'City', 'Doctor Count', 'Whatsapp', 'Download'];
	
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
