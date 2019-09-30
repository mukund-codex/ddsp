<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Feedback_report extends Reports_Controller
{
	private $module = 'feedback_report';
	private $controller = 'reports/feedback_report';
    private $model_name = 'mdl_feedback_report';
    private $columns = ['MR Name', 'HQ', 'Rating', 'Message', 'Date'];
    
	function __construct() {
		parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            $this->columns
        );
	}
}
