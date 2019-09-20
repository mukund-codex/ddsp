<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Derma_dr_report extends Reports_Controller
{
	private $module = 'derma_dr_report';
	private $controller = 'reports/derma_dr_report';
    private $model_name = 'mdl_derma_dr_report';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'MR Name', 'HQ', 'Chemist Name', 'Chemist Address' , 'Doctor Name', 'Doctor Address', 'Doctor Type', 'Category', 'Molecule Name', 'Brand Name', 'RXN', 'Date'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns,
        );
	}
}
