<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_user extends MY_Model {
	private $p_key = 'users_id';
	private $table = 'manpower';
	private $session_key;

	function __construct() {
		parent::__construct($this->table);
		$this->session_key = 'user_' . config_item('session_data_key');
	}

	function _authenticate($record){
		$id = $record[0]->users_id;
		$username = $record[0]->users_name;
		$a_type = $record[0]->users_type;

		$user_info = ['user_id' => $id, 'user_name' => $username, 'role' => $a_type, 'role_label' => $a_type];
		$this->session->set_userdata($this->session_key, $user_info);
		return true;
	}

	function authenticate(){
		$this->load->library('form_validation');

		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[15]|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

		if ( ! $this->form_validation->run() ){
			return FALSE;
		}
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$record = $this->get_records(['users_mobile' => $username, 'users_password' => $password, 'users_type' => 'HO']);
		
        if(! count($record)) {
			$record = $this->get_records(['users_emp_id' => $username, 'users_password' => $password, 'users_type' => ["ASM","ZSM"]]);
		}
		
		if(count($record)){
			return $this->_authenticate($record);
			$user = $this->session->userdata($this->session_key);
			return (  is_numeric($user['user_id']) ) ? TRUE : FALSE;
		}

		return FALSE;
	}

	function validate($type)
	{
		if($type == 'save') {
			return [
				[
					'field' => 'emp_id',
					'label' => 'Employee Id',
					'rules' => 'trim|required|xss_clean'
				],
				[
					'field' => 'rid',
					'label' => 'Employee Id',
					'rules' => 'trim|required|xss_clean'
				],
                [
					'field' => 'password',
					'label' => 'Password',
					'rules' => 'trim|required|min_length[8]|max_length[50]|xss_clean'
				],
				[
					'field' => 're_password',
					'label' => 'Re-enter Password',
					'rules' => 'trim|required|min_length[8]|max_length[50]|matches[password]|xss_clean'
				]
				
			];
		}
	}

	function change_password(){

		$this->load->library('form_validation');

		$rid = $this->input->post('rid');
        $emp_id = $this->input->post('emp_id');
        $password = $this->input->post('password');
		$re_password = $this->input->post('re_password');
		
		$this->form_validation->set_rules($this->validate('save'));
		
		if(! $this->form_validation->run()){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
				$errors[$key] = form_error($key, '<label class="error">', '</label>');
				
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}

		$request_token = base64_decode($rid);
        $forgotrecord = $this->model->get_records(['request_token' => $request_token, 'status' => 1], 'forgot_password_request');
        if(empty($forgotrecord)){
            $errors['emp_id'] = '<label class="error">Invalid User</label>';
            $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
        }

        $request_users_id = $forgotrecord[0]->users_id;

        $userdata = $this->model->get_records(['users_emp_id' => $emp_id], 'manpower');
        if(empty($userdata)){
            $errors['emp_id'] = '<label class="error">Invalid Employee ID</label>';
            $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
        }

        $users_id = $userdata[0]->users_id;

        if($request_users_id != $users_id){
            $errors['emp_id'] = '<label class="error">Invalid User</label>';
            $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
        }

        $data = [];
        $requestData = [];

        $data['users_password'] = $password;        
        $id = $this->model->_update(['users_id' => $users_id], $data, 'manpower');

        if($id){
            $requestData['status'] = 0;
            $request_id = $this->model->_update(['users_id' => $users_id,'request_token' => $request_token], $requestData, 'forgot_password_request');
            if(!empty($request_id)){
                $response['message'] = "Password Updated Successfully"; 
                $response['redirectTo'] = 'user/success';
				$response['status'] = TRUE;
				
				return $response;
            }
        }else{
			$response['errors'] = "Something went wrong, Please try again."; // Some might be empty
            $response['status'] = FALSE;
            
            return $response;      
        }

	}

}
