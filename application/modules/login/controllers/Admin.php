<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Login_Controller
{
	private $module = 'admin';
    private $model_name = 'mdl_admin';
    private $controller = 'login/admin';

	function __construct() {
        parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            ['SA', 'A']
        );
    }
}