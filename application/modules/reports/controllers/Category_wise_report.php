<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Category_wise_report extends Reports_Controller
{
	private $module = 'category_wise_report';
	private $controller = 'reports/category_wise_report';
    private $model_name = 'mdl_category_wise_report';
    private $columns = ['ZBM', 'Zone', 'ABM', 'Area', 'MR Name', 'HQ', 'AntiFungal', 'Acne Light', 'Hyper-Pigmentation'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
