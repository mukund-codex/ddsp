<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_admin extends MY_Model {

	private $p_key = 'user_id';
	private $table = 'admin';
	private $session_key;
    private $fillable = ['full_name', 'username', 'password', 'user_type', 'email', 'mobile'];

	function __construct() {
		parent::__construct($this->table, $this->p_key);
		$this->session_key = config_item('session_data_key');

	}

	function get_collection( $count = FALSE, $sfilters = [], $keywords = '', $limit = 0, $offset = 0, ...$params ) {
        
        $q = $this->db->select('
            admin.user_id, admin.full_name, 
            admin.username, admin.password, admin.user_type as admin_role_id, 
			admin.email, admin.mobile, admin.insert_dt,
			roles.role as user_type, roles.label as actual_user_type
    	')
		->from('admin')
		->join('roles', 'admin.user_type = roles.role_id', 'LEFT');

		if(sizeof($sfilters)) { 
			foreach ($sfilters as $key=>$value) { $q->where("$key", $value); }
		}

		$q->limit(1);
        
		$collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		// echo $this->db->last_query(); die();
		return $collection;
	}	

	function _authenticate($record){
		$admin_id = $record[0]['user_id'];
		$username = $record[0]['username'];
		$a_type = $record[0]['user_type'];
		$role_label = $record[0]['actual_user_type'];

		$admin_info = ['user_id'=> $admin_id, 'user_name'=>$username, 'role'=> $a_type, 'role_label'=> $role_label];
		$this->session->set_userdata($this->session_key, $admin_info);
		return true;
	}

	function authenticate(){
		$this->load->library(['form_validation','encryption']);

		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[15]|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

		if ( ! $this->form_validation->run() ){
			return FALSE;
		}
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$record = $this->get_collection(FALSE, ['username' => $username, 'admin.is_active' => 1]);
		
		if(count($record)){
			if(!Encryption::verifyPassword($password, $record[0]["password"])) {
				return FALSE;
			}

			return $this->_authenticate($record);
			/*check whether session is set*/
			$admin = $this->session->userdata($this->session_key);
			return (  is_numeric($admin['user_id']) ) ? TRUE : FALSE;
		}

		return FALSE;
	}
}
