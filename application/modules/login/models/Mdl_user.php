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
			$record = $this->get_records(['users_emp_id' => $username, 'users_password' => $password, 'users_type' => ["MR","ASM","RSM"]]);
		}

		if(count($record)){
			return $this->_authenticate($record);
			$user = $this->session->userdata($this->session_key);
			return (  is_numeric($user['user_id']) ) ? TRUE : FALSE;
		}

		return FALSE;
	}
}
