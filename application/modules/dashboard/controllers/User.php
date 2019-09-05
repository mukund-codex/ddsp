<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class User extends Front_Controller
{
	private $module = 'user';
	private $controller = 'dashboard/user';
	private $model_name = 'mdl_user';
	
	function __construct() {
		parent::__construct($this->module, $this->controller, $this->model_name);
	}

	function index(){
		if( ! $this->session->user_logged_in() ){
			redirect('user','refresh');
        }
        
        $user_id = $this->session->get_field_from_session('user_id', 'user');
		$role = $this->session->get_field_from_session('user_id', 'role');
		
		$data = [];
		if(in_array(strtoupper($role), ['MR','ASM','RSM'])) {
			$data['insert_user_id'] = $user_id;
		} 

		$this->data['doctor_count'] = count($this->model->get_records($data, 'doctor'));
		$this->data['patientCount'] = 0;

		$this->data['js'] = ['form-submit.js', 'common.js','doctor.js'];
		$this->data['plugins'] = ['countTo','select2','material-datetime','jCrop'];
        $this->data['mainmenu'] = 'dashboard';

    	$this->set_view($this->data, $this->controller . '/dashboard',  '_user');
    }
    
    function logout() {
        $session_key = 'user_' . config_item('session_data_key');
		$sessionData = array('user_id'=>'',	'user_name'=>'', 'role'=>'');
		
		$this->session->unset_userdata($session_key, $sessionData);
		redirect('/user','refresh');
    }
}
