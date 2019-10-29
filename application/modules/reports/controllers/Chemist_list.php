<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Chemist_list extends Reports_Controller
{
	private $module = 'chemist_list';
	private $controller = 'reports/chemist_list';
    private $model_name = 'mdl_chemist_list';
    private $columns = ['ASM', 'Area', 'MR Name', 'HQ', 'Chemist Name', 'Chemist Location', 'Date'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
