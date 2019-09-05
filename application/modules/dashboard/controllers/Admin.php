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
    	$this->set_view($this->data, $this->controller . '/dashboard',  '_admin');
    }
    
    function logout() {
        $session_key = config_item('session_data_key');
		$sessionData = array('user_id'=>'',	'user_name'=>'', 'role'=>'');
		
		$this->session->unset_userdata($session_key, $sessionData);
     	redirect('/admin','refresh');
    }
}
