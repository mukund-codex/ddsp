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
        
        $rid = $this->input->get('rid');
        
        if(empty($rid)){
            show_404();
        }

        $request_token = base64_decode($rid);        

        $requestdata = $this->model->get_records(['request_token' => $request_token, 'status' => 1], 'forgot_password_request');

        if(empty($requestdata)){
            show_404();
        }

        $data['users_id'] = $requestdata[0]->users_id;
        $data['request_token'] = $rid;

        $this->set_view($data, 'user/forgot_password');
    
    }

	function change_password(){
        
        $rid = $this->input->post('rid');
        $emp_id = $this->input->post('emp_id');
        $password = $this->input->post('password');
        $re_password = $this->input->post('re_password');

        if(empty($rid)){
            echo "Invalid User";
            exit;
        }

        if(empty($emp_id)){
            echo "Invalid Employee ID";
            exit;
        }

        if(empty($password)){
            echo "Please enter password";
            exit;
        }

        if(empty($re_password)){
            echo "Please re-enter password";
            exit;
        }

        if($password != $re_password){
            echo "Password Mismatch";
            exit;
        }

        $request_token = base64_decode($rid);
        $forgotrecord = $this->model->get_records(['request_token' => $request_token], 'forgot_password_request');
        if(empty($forgotrecord)){
            echo "Invalid User";
            exit;
        }

        $request_users_id = $forgotrecord[0]->users_id;

        $userdata = $this->model->get_records(['users_emp_id' => $emp_id], 'manpower');
        if(empty($userdata)){
            echo "Invalid Employee ID";
            exit;
        }

        $users_id = $userdata[0]->users_id;

        if($request_users_id != $users_id){
            echo "Invalid User";
            exit;
        }

        $data = [];
        $requestData = [];

        $data['users_password'] = $password;        
        $id = $this->model->_update(['users_id' => $users_id], $data, 'manpower');

        if(!empty($id)){
            $requestData['status'] = 0;
            $request_id = $this->model->_update(['users_id' => $users_id], $requestData, 'forgot_password_request');
            if(!empty($request_id)){
                echo "Password Updated Successfully";
            }
        }else{
            echo "Something went wrong, Please try again.";
            exit;        
        }

	}

}