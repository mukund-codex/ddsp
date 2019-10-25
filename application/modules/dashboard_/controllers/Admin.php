<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller
{
	private $module = 'admin';
	private $controller = 'dashboard/admin';
	private $model_name = 'mdl_admin';
	private $settings = [
        'permissions'=> ['add', 'edit', 'download', 'upload', 'remove'],
        'paginate_index' => 3
    ];
	private $scripts = ['load-geography.js'];
	
	function __construct() {
		parent::__construct($this->module, $this->controller, $this->model_name, $this->settings, $this->scripts);
	}

	function index(){
		if( ! $this->session->is_admin_logged_in() ){
			redirect('admin','refresh');
		}

		$this->data['mainmenu'] = 'dashboard';

		$data = $this->model->getDashboardCounts();

		$this->data['chemist_count'] = $data[0]->chemist_count;
		$this->data['doctor_count'] = $data[0]->doctor_count;
		
    	$this->set_view($this->data, $this->controller . '/dashboard',  '_admin');
    }
    
    function logout() {
        $session_key = config_item('session_data_key');
		$sessionData = array('user_id'=>'',	'user_name'=>'', 'role'=>'');
		
		$this->session->unset_userdata($session_key, $sessionData);
     	redirect('/admin','refresh');
    }
}
