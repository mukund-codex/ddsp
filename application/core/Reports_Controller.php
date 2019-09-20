<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
abstract class Reports_Controller extends Generic_Controller{
	private $scripts;
    private $controller; 
    private $module; 
    private $columns;

    function __construct($module, $controller, $model_name, $columns = [], $scripts = []){
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
		$this->columns = $columns;

		$this->data['settings'] = $config; 
		$this->set_defaults();
    }

    protected function set_defaults($defaults = []) {
        $this->set_default_data($defaults, $this->module, $this->controller);            
    }
    
    function index(){
		if( ! $this->session->is_logged_in() ){
			show_error("Forbidden", 403);
		}

		$sfilters = array();

        $offset = (int) $this->input->post('page');
        // print_r(print_r($_POST)); die();
        // $keywords = !empty($this->input->post('keywords'))?$this->input->post('keywords'):'';

        $post_array = $this->input->post();
        unset($post_array['page']);
        unset($post_array['search']);       
		
		$this->data['collection'] = $this->model->get_collection($count = FALSE, $sfilters, $post_array, $this->perPage, $offset);
        $totalRec = $this->model->get_collection($count = TRUE, $sfilters, $post_array);
        // print_r($totalRec);
        // echo $this->db->last_query(); 
        $this->paginate($this->data['controller'], $totalRec, 4);
		$this->data['plugins'] = ['paginate','fancybox'];
        
        /* columns for list */
        $table_columns = $this->columns;
        $this->data['all_action'] = FALSE;

        $this->set_defaults([
            'listing_url'=> $this->controller . '/index', 
            'download_url'=> $this->controller . '/download' ,
            'module_title'=> $this->module . ''
        ]);
        $this->set_view_columns($table_columns);
        /* END columns */
        $records_view = $this->data['controller'].'/records';
        
        $role = $this->session->get_field_from_session('role');

        $this->data['permissions'] = ['download'];
        
        $template = ( in_array($role, ['SA', 'A'])) ? '_admin' : '_user';

        $filter_columns = $this->model->get_filters();
        
        $this->data['show_filters'] = TRUE;
        $this->data['date_filters'] = TRUE;
        
        $this->set_view_columns($table_columns, [], $filter_columns);

        if($this->input->post('search') == TRUE) {
			$this->load->view($records_view, $this->data);
        } else {
            $this->data['records_view'] = $records_view;
			$this->set_view($this->data, 'template/components/container/lists', $template);
		}
	}

	function download(){

		if( ! $this->session->is_logged_in() )
            show_error("Forbidden", 403);
            

        $get_request = $this->input->get();
        // print_r($get_request); die();
        $new_request = array_filter($get_request, function($v) { return $v !== ''; } ); 

		$data = $this->model->get_collection($count = FALSE, [], $new_request);
		$fields = $this->model->_format_data_to_export($data);

		$this->download_file($this->data['controller'], $fields);
	}
}