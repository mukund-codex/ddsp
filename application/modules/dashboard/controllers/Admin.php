<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller
{
	private $module = 'admin';
	private $controller = 'dashboard/admin';
	private $model_name = 'mdl_admin';
	
	function __construct() {
		parent::__construct($this->module, $this->controller, $this->model_name);
	}

	function index(){
		if( ! $this->session->is_admin_logged_in() ){
			redirect('admin','refresh');
		}

		$this->data['mainmenu'] = 'dashboard';

		$chemist_data = $this->model->get_chemist_count();
		$count = $chemist_data[0]['chemist_count'];
		$chemist_count = empty($count) ? 0 : $count;
		$this->data['chemist_count'] = $chemist_count;

		$doctor_data = $this->model->get_doctor_count();
		$dcount = $doctor_data[0]['doctor_count'];
		$doctor_count = empty($dcount) ? 0 : $dcount;
		$this->data['doctor_count'] = $doctor_count;
		
    	$this->set_view($this->data, $this->controller . '/dashboard',  '_admin');
    }
    
    function logout() {
        $session_key = config_item('session_data_key');
		$sessionData = array('user_id'=>'',	'user_name'=>'', 'role'=>'');
		
		$this->session->unset_userdata($session_key, $sessionData);
     	redirect('/admin','refresh');
    }
}
