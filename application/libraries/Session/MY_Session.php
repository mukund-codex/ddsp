<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class MY_Session extends CI_Session {
    private $session_key;

    function __construct(){
        parent::__construct();
        $this->session_key = config_item('session_data_key');
    }

    public function array_keys_exist( array $array, $keys ) {
	    $count = 0;
	    if ( ! is_array( $keys ) ) {
	        $keys = func_get_args();
	        array_shift( $keys );
	    }
	    foreach ( $keys as $key ) {
	        if ( array_key_exists( $key, $array ) ) {
	            $count ++;
	        }
	    }

	    return count( $keys ) === $count;
	}

	public function is_admin_logged_in(){
		$is_session_set = $this->userdata($this->session_key);

		if( $is_session_set ){
			return ( $this->array_keys_exist( $is_session_set, ['user_id', 'user_name', 'role', 'role_label']) ) ? TRUE : FALSE;
		}
		else{
			return FALSE;
		}
    }
    
    public function user_logged_in(){
		$is_session_set = $this->userdata('user_' . $this->session_key);
		
		if( $is_session_set ){
			return ( $this->array_keys_exist( $is_session_set, ['user_id', 'user_name']) ) ? TRUE : FALSE;
		}
		else{
			return FALSE;
		}
	}

	public function get_field_from_session($field = 'role', $type = 'admin'){

        $key_string = ($type == 'admin') ? $this->session_key : 'user_' . $this->session_key;

		$session_data = $this->userdata($key_string);
		
		if( !$session_data || empty($session_data) ){ return FALSE; }

		switch($field){
			case 'user_id':
				$value = $session_data['user_id']; break;
			case 'user_name':
				$value = $session_data['user_name']; break;
            case 'role':
				$value = $session_data['role']; break;
            case 'role_label':
				$value = $session_data['role_label']; break;
            default:
				$value = $session_data['role'];
		}

		return $value;
	}

	public function is_Ajax(){
		$CI = &get_instance();
		return ($CI->input->is_ajax_request()) ? TRUE : FALSE;
	}

	public function is_Ajax_and_logged_in(){
		if( ! $this->is_Ajax() ){ show_error("Bad Request", 400); }

        $admin_login_status = (int) $this->is_admin_logged_in();
		$user_login_status = (int) $this->user_logged_in();
		
		if( ! $admin_login_status && ! $user_login_status){
			echo json_encode(['status'=> FALSE, 'error'=> 'Forbidden']); exit;
		}
    }
    
	public function is_logged_in(){
        $admin_login_status = (int) $this->is_admin_logged_in();
		$user_login_status = (int) $this->user_logged_in();

		if( ! $admin_login_status && ! $user_login_status){
			echo json_encode(['status'=> FALSE, 'error'=> 'Forbidden']); exit;
		}
		return true;
    }
    
    public function is_Ajax_and_user_logged_in(){
		if( ! $this->is_Ajax() ){ show_error("Bad Request", 400); }

		$login_status = (int) $this->user_logged_in();

		if( ! $login_status){
			echo json_encode(['status'=> FALSE, 'error'=> 'Forbidden']); exit;
		}
    }
    
    public function is_Ajax_and_admin_logged_in(){
		if( ! $this->is_Ajax() ){ show_error("Bad Request", 400); }

		$admin_login_status = (int) $this->is_admin_logged_in();

		if( ! $admin_login_status){
			echo json_encode(['status'=> FALSE, 'error'=> 'Forbidden']); exit;
		}
	}
}  