<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class User extends Login_Controller
{
	private $module = 'user';
    private $model_name = 'mdl_user';
    private $controller = 'login/user';
    private $scripts = ['form-submit.js'];

	function __construct() {
        parent::__construct(
            $this->module, 
            $this->controller, 
            $this->model_name, 
            ['MR', 'HO', 'ASM', 'RSM'],
            $this->scripts
        );
    }

    function forgot_password(){

        $this->data['js'] = $this->scripts;
        
        $rid = $this->input->get('rid');
        
        if(empty($rid)){
            show_404();
        }

        $request_token = base64_decode($rid);
        $requestdata = $this->model->get_records(['request_token' => $request_token, 'status' => 1], 'forgot_password_request');

        if(empty($requestdata)){
            show_404();
        }

        $this->data['users_id'] = $requestdata[0]->users_id;
        $this->data['request_token'] = $rid;

        $this->set_view($this->data, 'forgot_password', '_login');
    }

    function change_password(){

		$result = $this->model->change_password();
		echo json_encode($result);
    }
    
    function success(){
        $this->set_view($this->data, 'success', '_login');
    }

}