<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Region_wise extends Reports_Controller
{
	private $module = 'region_wise';
	private $controller = 'reports/region_wise';
    private $model_name = 'mdl_region_wise';
    private $columns = ['RSM Name', 'Region', 'Doctor Count'];
	
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
