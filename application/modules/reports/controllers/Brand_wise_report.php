<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Brand_wise_report extends Reports_Controller
{
	private $module = 'brand_wise_report';
	private $controller = 'reports/brand_wise_report';
    private $model_name = 'mdl_brand_wise_report';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'MR Name', 'HQ', 'Chemist Name', 'Chemist Address', 'Doctor Name', 'Doctor Address', 'Molecule Name', 'Brand Name', 'RXN'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
