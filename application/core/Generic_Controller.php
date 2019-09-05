<?php (defined('BASEPATH')) OR exit('No direct script access allowed'); 
abstract class Generic_Controller extends MX_Controller{
	protected $data = [];
	protected $perPage;
	protected $dropdownlength;

    function __construct(){
        parent::__construct();
		$this->data['timestamp'] = time();
		$this->perPage = PAGINATION_PAGE;
		$this->dropdownlength = PAGINATE_OPTIONS;
	}
	
	protected function get_template(){
		$role = $this->session->get_field_from_session();
		
		if($role == 'SA'){$template = '_admin';}
		if($role == 'HO'){$template = '_ho';}
		if($role == 'ASM'){$template = '_user';}
		if($role == 'DR'){$template = '_doctor';}

		return $template;
    }

    protected function paginate($module = '', $totalRec = '', $uri_segment = 3, $perPage = '' ){

		$this->load->library('Ajax_pagination');

	 	$config['first_link']  = 'First';
        $config['div']         = 'records-list'; //parent div tag id
        $config['base_url']    = base_url(). $module .'/results';
        $config['total_rows']  = $totalRec;
        $config['per_page']    = (!empty($perPage)) ? $perPage : $this->perPage;
        $config['anchor_class']= 'page-bullets';
        $config['uri_segment'] = $uri_segment;

        $this->ajax_pagination->initialize($config);
	}

	protected function set_view($data = [], $view = '', $template = '',  $title = ''){
        
		$template = $template;
		$data['viewFile'] = (! empty($view)) ? $view : 'no-view';
		
		$pg_title = (! empty($title) ) ? $title : (isset($data['section_title']) ? $data['section_title'] : config_item('title'));
        $data['pg_title'] = ucwords(str_replace('_', ' ', $pg_title));
        
		if( ! empty($template) ){
			echo Modules::run('template/'. $template, $data);
		}
		else{
			echo $this->load->view($view, $data);
		}
    }

    protected function set_default_data($defaults = [], $module, $controller) {
        $this->data['controller']     = (isset($defaults['controller'])) ? $defaults['controller'] : $controller;
        $this->data['mainmenu']       = (isset($defaults['mainmenu'])) ? $defaults['mainmenu'] : $module;
        $this->data['menu']           = (isset($defaults['menu'])) ? $defaults['menu'] : $module;
        $this->data['module']         = (isset($defaults['module'])) ? $defaults['module'] : $module;
        $this->data['module_title']   = (isset($defaults['module_title'])) ? $defaults['module_title'] : $module;

        $this->data['section_title']  = (isset($defaults['section_title'])) ? $defaults['section_title'] : $controller;
        $this->data['cancel_url']     = (isset($defaults['cancel_url'])) ? $defaults['cancel_url'] : $controller . '/lists';
        $this->data['listing_url']    = (isset($defaults['listing_url'])) ? $defaults['listing_url'] : $controller . '/lists';
        $this->data['download_url']   = (isset($defaults['download_url'])) ? $defaults['download_url'] : $controller . '/download';
    }
    
    protected function set_view_columns($tbl_column = [] , $csv_columns = [], $filter_columns = []) {
        $this->data['columns'] = $tbl_column;
        $this->data['csv_fields'] = $csv_columns;
        $this->data['filter_columns'] = $filter_columns;
    }
    
	protected function download_file($title = 'Excel', $fields = []){

		$this->load->library('export');
		$report_title = ucfirst($title) . '_Report-' . date('Y-m-d') . '.xls';
		$this->export->download_send_headers( $report_title );

		$this->export->array2csv($fields);
	}

}