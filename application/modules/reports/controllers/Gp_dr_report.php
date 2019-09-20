<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Gp_dr_report extends Reports_Controller
{
	private $module = 'gp_dr_report';
	private $controller = 'reports/gp_dr_report';
    private $model_name = 'mdl_gp_dr_report';
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
