<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Basic extends Admin_Controller
{
	private $module = 'basic';
	private $controller = 'settings/basic';
	private $model_name = 'mdl_basic';
	private $settings = [
        'permissions'=> ['add','edit','remove','download'],
	];
	
	function __construct() {
		parent::__construct($this->module, $this->controller, $this->model_name, $this->settings);
    }
    
    function add(){
		if( ! $this->session->is_admin_logged_in() ){
			redirect('admin','refresh');
		}
		
		$this->data['js']	= ['form-submit.js'];

		$this->data['themes'] = [
			'pink', 'purple', 'deep-purple', 'indigo', 
			'blue', 'light-blue', 'cyan', 'teal', 
			'green', 'light-green', 'lime', 'yellow', 
			'amber', 'orange', 'deep-orange', 
			'brown', 'grey', 'blue-grey', 'black'
		];
		
		$this->set_view($this->data, 'template/components/container/add',  '_admin');
	}
}