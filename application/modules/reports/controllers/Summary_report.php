<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Summary_report extends Reports_Controller
{
	private $module = 'summary_report';
	private $controller = 'reports/summary_report';
    private $model_name = 'mdl_summary_report';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'Total Chemist Count', 'No. of Reps', 'No. of Days', 'Chemist Average = Total chemist count/(no. of reps*no.of days)', 'Total Doctor Count', 'ABM Approved Count', 'ZBM Approved Count'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
