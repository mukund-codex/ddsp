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
        
        $request_token = $_GET['rid'];
        
        if(empty($request_token)){
            show_404();
        }

        $requestdata = $this->model->get_records(['request_token' => $request_token], 'forgot_password_request');

        if(empty($requestdata)){
            show_404();
        }
        $data['users_id'] = $requestdata[0]->users_id;

        $this->set_view($data, 'user/forgot_password');
    
    }

}