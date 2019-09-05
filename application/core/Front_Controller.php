<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
abstract class Front_Controller extends Generic_Controller{
    private $scripts;
    private $controller; 
    private $module; 

    function __construct($module, $controller, $model_name, $scripts = []){
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

		$this->data['settings'] = $config; 
		$this->set_defaults();
    }

    protected function set_defaults($defaults = []) {
        $this->set_default_data($defaults, $this->module, $this->controller);            
	}
	
	protected function save(){
		$this->session->is_Ajax_and_user_logged_in();

		$result = $this->model->save();
		echo json_encode($result);
	}

	protected function modify(){
		$this->session->is_Ajax_and_user_logged_in();

        $result = $this->model->modify();
        
        if($result['status']) {
            $result['message'] = 'Record Updated Successfully!';
        }
		echo json_encode($result);
	}

	protected function remove(){
		$this->session->is_Ajax_and_user_logged_in();

		$response = $this->model->remove();
		echo json_encode($response);
	}

	function download(){

		if( ! $this->session->user_logged_in() )
			redirect('login','refresh');

		$keywords = (isset($_GET['keywords'])) ? $_GET['keywords'] : '';

		$data = $this->model->get_collection($count = FALSE, [], $keywords);
		$fields = $this->model->_format_data_to_export($data);

		$this->download_file($this->data['controller'] . '-' . date('Y-m-d'), $fields);
	}
}