<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Login_reports extends Reports_Controller
{
	private $module = 'login_reports';
	private $controller = 'reports/login_reports';
    private $model_name = 'mdl_login_reports';
    private $columns = ['NSM', 'National Zone', 'ZBM Name', 'Zone', 'ABM Name', 'Area', 'MR Name', 'HQ', 'MR Mobile', 'Last Login Date and Time'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
