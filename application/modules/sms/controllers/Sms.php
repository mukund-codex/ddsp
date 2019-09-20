<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Sms extends Admin_Controller
{
	private $module = 'sms';
	private $controller = 'sms';
	private $model_name = 'mdl_sms';
	private $csv_fields;
	private $settings = [
        'permissions'=> ['addSMSbalance','download'],
    ];

	function __construct() {
		parent::__construct($this->module, $this->controller, $this->model_name, $this->settings);
        $this->data['columns'] = ['Message For', 'Mobile', 'Message', 'Response', 'Date'];
	}

	function options(){
		$this->session->is_admin_logged_in();

		$limit = $this->dropdownlength;
		$page = intval($_POST['page']) - 1;
		$page = ($page <= 0) ? 0 : $page;

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'sms_name', [], $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'sms_name'));

		foreach($_options as $option){
			$new['id'] = $option->sms_id;
			$new['text'] = $option->sms_name;

			array_push($json['results'], $new);
		}
		
		$more = ($_opt_count > count($_options)) ? TRUE : FALSE;
		$json['pagination']['more'] = $more;

		echo json_encode($json);
	}

	function lists(){
		
		if( ! $this->session->is_admin_logged_in() ){
			redirect('admin/login','refresh');
		}

		$sfilters = array();

		$sfilters = array();
        $offset = (int) $this->input->post('page');
        $post_array = $this->input->post();
        unset($post_array['page']);
        unset($post_array['search']);
        
        $this->data['collection'] = $this->model->get_collection($count = FALSE, $sfilters, $post_array, $this->perPage, $offset);
		$totalRec = $this->model->get_collection( $count = TRUE, $sfilters, $post_array);
		
		$this->paginate($this->data['controller'], $totalRec);

        $csv_fields = [];
        $this->data['csv_fields'] = $this->csv_fields;
		$this->data['plugins'] = ['paginate'];
		$this->data['permissions'] = $this->settings['permissions'];
		$this->data['all_action'] = FALSE;
		$this->data['date_filters'] = TRUE;
		$this->data['show_filters'] = TRUE;

		$filter_columns = $this->model->get_filters();
		$this->data['filter_columns'] = $filter_columns;
		
		$this->data['listing_url'] = $this->data['controller'] . '/lists';
		$this->data['download_url'] = $this->data['controller'] . '/download';

		$this->data['total_balance'] = $this->model->total_sms();
		$this->data['sms_balance'] = $this->model->get_sms_balance();

        $title_txt = 'Manage '. ucfirst($this->module);

		$records_view = $this->data['controller'].'/results';

		if($this->input->post('search') == TRUE) {
			$this->load->view($records_view, $this->data);
        } else {
			$this->data['records_view'] = $records_view;
			$this->set_view($this->data, 'template/components/container/lists',  '_admin');
		}
	}

	function add(){
		if( ! $this->session->is_admin_logged_in() )
			redirect('admin/login','refresh');

		$this->data['js'] = ['form-submit.js'];
		$this->data['plugins'] = ['select2'];
		$title_txt = 'Send '. ucfirst($this->module);

		$this->set_view($this->data, $this->controller.'/add', '_admin', $title_txt);
	}

	function addSmsBalance() {
		$title_txt = 'Add SMS Balance';
		$this->data['js'] = ['form-submit.js'];
		$this->set_view($this->data, $this->controller.'/addSmsBalance', '_admin', $title_txt);
	}

	function saveSmsBalance() {
		$this->session->is_Ajax_and_admin_logged_in();
		$result = $this->model->saveSmsBalance();
		echo json_encode($result);
	}

	function save(){
		$this->session->is_Ajax_and_admin_logged_in();

		$result = $this->model->save();
		echo json_encode($result);
	}

	function remove(){
		$this->session->is_Ajax_and_admin_logged_in();

		$response = $this->model->remove();
		echo json_encode($response);
	}
}
