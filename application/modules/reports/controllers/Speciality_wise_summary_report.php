<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Speciality_wise_summary_report extends Reports_Controller
{
	private $module = 'speciality_wise_summary_report';
	private $controller = 'reports/speciality_wise_summary_report';
    private $model_name = 'mdl_speciality_wise_summary_report';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'No. of Days', 'Total Chemist Count', 'Total Doctor Count', 'Derma', 'CP', 'GP', 'Gynae'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
