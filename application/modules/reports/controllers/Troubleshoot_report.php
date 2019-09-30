<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Troubleshoot_report extends Reports_Controller
{
	private $module = 'troubleshoot_report';
	private $controller = 'reports/troubleshoot_report';
    private $model_name = 'mdl_troubleshoot_report';
    private $columns = ['MR Name', 'HQ', 'Message', 'Images', 'Date'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
