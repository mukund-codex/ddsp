<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mr_summary_report extends Reports_Controller
{
	private $module = 'mr_summary_report';
	private $controller = 'reports/mr_summary_report';
    private $model_name = 'mdl_mr_summary_report';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'MR', 'City', 'Total Chemist Count', 'No. of Reps', 'No. of Days', 'Chemist Average = Total chemist count/(no. of reps*no.of days)', 'Total Doctor Count', 'ABM Approved Count', 'ZBM Approved Count'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
