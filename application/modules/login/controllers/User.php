<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class User extends Login_Controller
{
	private $module = 'user';
    private $model_name = 'mdl_user';
    private $controller = 'login/user';

	function __construct() {
        parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            ['MR', 'HO', 'ASM', 'RSM']
        );
    }

    function forgot_password(){
        echo "aashdgfsahdgsavdngvsad";exit;
    }

}