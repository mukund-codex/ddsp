<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Livetracker extends Reports_Controller
{
	private $module = 'livetracker';
	private $controller = 'reports/livetracker';
    private $model_name = 'mdl_livetracker';
    private $columns = ['Manager Name', 'Manager Type', 'Employee', 'Designation', 'EMP ID', 'Zone', 'Region', 'Area', 'City', 'Doctor', 'Mobile', 'Image', 'Message', 'Date'];
	
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
