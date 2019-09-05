<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
abstract class Login_Controller extends Generic_Controller{
	private $scripts;
    private $controller; 
    private $module; 
    private $roles;

    function __construct($module, $controller, $model_name, $roles = [], $scripts = []){
        parent::__construct();
        $this->load->model($model_name, 'model');

		$admin_config = $this->model->get_records([], 'config', ['config_type', 'config_value']);
		
		$config = [];
		foreach($admin_config as $record){
			$config[$record->config_type] = $record->config_value;
		}

		$this->scripts = $scripts;
		$this->controller = $controller;
        $this->module = $module;
        $this->roles = $roles;

        $this->data['settings'] = $config; 
        $this->data['module'] = $this->module;
        $this->data['controller'] = $this->controller;

    }

    function index() {
        $status = (in_array('SA', $this->roles)) ? $this->session->is_admin_logged_in() : $this->session->user_logged_in();

        if( $status ){

			$role = (in_array('SA', $this->roles)) ? $this->session->get_field_from_session() : $this->session->get_field_from_session('role', 'user');

			if( in_array($role, $this->roles) ){
				redirect('dashboard/'. $this->module,'refresh');
			}
		}

        
		$this->set_view($this->data, 'login',  '_login');
    }

    function submit(){
		if(! $this->input->post()){ show_404();	}

		/* if(isset($_POST['g-recaptcha-response'])) { 			
			$captcha = $this->input->post('g-recaptcha-response');
			$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . RE_CAPTCHA_SECRET_KEY . "&response=" . $captcha); 			
			$responseKeys = json_decode($response, true);
			
			if (intval($responseKeys["success"]) == 1) {	 */	
				$status = $this->model->authenticate();
		
				if($status){
					redirect('dashboard/' . $this->module ,'refresh');
				}
				
				$errors = validation_errors();
				if(empty($errors)) {
					$this->data['error_msg'] = 'Username and Password don\'t match';
				}
			/* } else {
				$this->data['error_msg'] = 'Incorrect Captcha';
			}
		}	 */
		
		$this->set_view($this->data, 'login',  '_login');
	}
}