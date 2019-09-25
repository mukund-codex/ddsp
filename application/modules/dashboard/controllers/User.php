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

		$this->data['js'] = ['form-submit.js', 'common.js','doctor.js'];
		$this->data['plugins'] = ['countTo','select2','material-datetime','jCrop'];
        $this->data['mainmenu'] = 'dashboard';

		$chemist_data = $this->model->get_chemist_count();
		$count = $chemist_data[0]['chemist_count'];
		$chemist_count = empty($count) ? 0 : $count;
		$this->data['chemist_count'] = $chemist_count;

		$doctor_data = $this->model->get_doctor_count();
		$dcount = $doctor_data[0]['doctor_count'];
		$doctor_count = empty($dcount) ? 0 : $dcount;
		$this->data['doctor_count'] = $doctor_count;

		$app_doctor_data = $this->model->get_approved_doctor_count();
		$acount = $app_doctor_data[0]['doctor_count'];
		$adoctor_count = empty($acount) ? 0 : $acount;
		$this->data['approved_doctor_count'] = $adoctor_count;

		$dapp_doctor_data = $this->model->get_disapproved_doctor_count();
		$dcount = $dapp_doctor_data[0]['doctor_count'];
		$dadoctor_count = empty($dcount) ? 0 : $dcount;
		$this->data['disapproved_doctor_count'] = $dadoctor_count;

		//echo '<pre>';print_r($this->data);exit;

    	$this->set_view($this->data, $this->controller . '/dashboard',  '_user');
    }
    
    function logout() {
        $session_key = 'user_' . config_item('session_data_key');
		$sessionData = array('user_id'=>'',	'user_name'=>'', 'role'=>'');
		
		$this->session->unset_userdata($session_key, $sessionData);
		redirect('/user','refresh');
    }
}
