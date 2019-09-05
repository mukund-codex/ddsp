<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
abstract class User_Controller extends Generic_Controller{
	private $scripts;
    private $controller; 
    private $module; 
    private $settings;
    private $permissions;
    private $template;
    private $role;
    private $plugins;

    function __construct($module, $controller, $model_name, $settings=[], $scripts = [], $plugins = []){

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
        $this->plugins = $plugins;

        $this->settings['permissions'] = array_key_exists('permissions', $settings) ? $settings['permissions'] : [];
        $this->settings['paginate_index'] = array_key_exists('paginate_index', $settings) ? $settings['paginate_index'] : 3;
        $this->settings['filters'] = array_key_exists('filters', $settings) ? $settings['filters'] : ['column_filters'=> TRUE, 'date_filters'=> TRUE];

        $this->permissions = $this->settings['permissions'];

        $this->data['settings'] = $config; 
        $this->set_defaults();
        
        $this->role = $this->session->get_field_from_session('role','user');

        if(empty($this->role)) {
            $this->role = $this->session->get_field_from_session('role');
        }
        
        $this->data['role'] = $this->role;
        $this->template = ( in_array($this->role, ['SA', 'A'])) ? '_admin' : '_user';
    }

    protected function set_defaults($defaults = []) {
        $this->set_default_data($defaults, $this->module, $this->controller);            
    }

    function lists(){
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
		$totalRec = $this->model->get_collection( $count = TRUE, $sfilters, $post_array);
        
        $this->paginate($this->data['controller'], $totalRec, $this->settings['paginate_index']);
        array_unshift($this->plugins, 'paginate');
        $this->data['plugins'] = $this->plugins;
        
        /* columns for list & CSV */
        $table_columns = $this->model->get_column_list();
        $csv_columns = $this->model->get_csv_columns();

        if(!in_array('remove', $this->permissions)) {
            $this->data['all_action'] = FALSE;
        }
       
       /*   $filter_columns = $this->model->get_filters();
        if(count($filter_columns)) {
            $this->data['show_filters'] = TRUE;
        }
        $this->data['date_filters'] = TRUE; */

        $filter_columns = $this->model->get_filters();
        
        $this->data['show_filters'] = array_key_exists('show_filters', $this->settings['filters']) ? $this->settings['filters']['show_filters'] : TRUE;
        $this->data['date_filters'] = array_key_exists('date_filters', $this->settings['filters']) ? $this->settings['filters']['date_filters'] : TRUE;
        
        $this->set_view_columns($table_columns, $csv_columns, $filter_columns);
        /* END columns */
		
        $records_view = $this->data['controller'].'/records';
        $this->data['js'] = $this->scripts;
        $this->data['permissions'] = $this->settings['permissions'];
        // echo '<pre>';print_r($this->data); die();
		if ($this->input->post('search') == TRUE) {
			$this->load->view($records_view, $this->data);
        }else
        {
			$this->data['records_view'] = $records_view;
			$this->set_view($this->data, 'template/components/container/lists', $this->template);
		}
	}

    function add(){
    	if( ! $this->session->is_logged_in() )
			show_error("Forbidden", 403);

        if(!in_array('add', $this->permissions)) {
            show_error('You Don\'t have access to view this page', 403, 'Access Denied');
        }   

		array_unshift($this->scripts, 'form-submit.js');
		$this->data['js'] = $this->scripts;

        array_unshift($this->plugins, 'select2');
		$this->data['plugins'] = $this->plugins;
		$this->data['section_title'] = 'Add '. ucfirst($this->data['module']);

        $this->set_view($this->data, 'template/components/container/add', $this->template);
	}

	function edit(){
		if( ! $this->session->is_logged_in() )
			show_error("Forbidden", 403);

        if(!in_array('edit', $this->permissions)) {
            show_error('You Don\'t have access to view this page', 403, 'Access Denied');
        } 

        $uri_string = $this->uri->uri_string();

        if(! strpos($uri_string, 'record')) {
            show_404();
        }

        $revised_url = strstr($uri_string, 'record');

        list($k, $v) = explode('/', $revised_url);
        $array[$k] = $v;

        // $array = $this->uri->uri_to_assoc(); 
        /* print_r($array); 
        die();
	 */
        $tb_alias = $this->model->get_alias();

        $alias = (!empty($tb_alias)) ? $tb_alias : '';
        $table = $this->model->get_table();
        $p_key = $this->model->get_pkey();

        if(!isset($array['record'])) {
			show_404();
		}

		$temp = [];
		foreach($array as $i=>$value) {
			if($i == 'record') {
				$i = $p_key;
				$this->data[$i] = $id = (int) $array['record'];
			}
			$tempKey = (empty($alias)) ? "$table.$i" : "$alias.$i";
			$temp[$tempKey] = $value;
		}

		$this->data['info'] = $this->model->get_collection(FALSE, $temp);

		if(! count($this->data['info']) ){ show_404(); }

		array_unshift($this->plugins, 'select2');
        $this->data['plugins'] = $this->plugins;
        
		array_unshift($this->scripts, 'form-submit.js');
        $this->data['js'] = $this->scripts;
        
        $this->data['section_title'] = 'Edit '. ucfirst($this->data['module']);
        $this->set_view($this->data, 'template/components/container/edit', $this->template);
	}

	function save(){
		$this->session->is_Ajax_and_logged_in();

        if(!in_array('add', $this->permissions)) {
            print_r(['status'=> FALSE, 'message'=> 'This action is not allowed']);
            exit;
        } 

		$result = $this->model->save();
		echo json_encode($result);
	}

	function modify(){
		$this->session->is_Ajax_and_logged_in();

        if(!in_array('edit', $this->permissions)) {
            echo ['status'=> FALSE, 'message'=> 'This action is not allowed']; 
            exit;
        }

        $result = $this->model->modify();
        
        if($result['status']) {
            $result['message'] = 'Record Updated Successfully!';
        }
		echo json_encode($result);
	}

	function remove(){
		$this->session->is_Ajax_and_logged_in();

        if(!in_array('remove', $this->permissions)) {
            echo ['status'=> FALSE, 'message'=> 'This action is not allowed'];
        }

		$response = $this->model->remove();
		echo json_encode($response);
	}
	
	function remove_single_row() {
		if( ! $this->session->is_logged_in() )
            show_error("Forbidden", 403);
            
        if(!in_array('remove', $this->permissions)) {
            show_error('You Don\'t have access to view this page', 403, 'Access Denied');
        } 

		$response = $this->model->remove($this->input->get());
		echo json_encode($response);
	}

	function download(){

		if( ! $this->session->is_logged_in() )
            show_error("Forbidden", 403);
            
        if(!in_array('download', $this->permissions)) {
            show_error('You Don\'t have access to view this page', 403, 'Access Denied');
        } 

        $get_request = $this->input->get();
        $new_request = array_filter($get_request, function($v) { return $v !== ''; } ); 
        
		$data = $this->model->get_collection($count = FALSE, [], $new_request);
		$fields = $this->model->_format_data_to_export($data);

		$this->download_file($this->data['controller'], $fields);
	}
}