<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
class Password extends Admin_Controller
{
	private $module = 'password';
    private $model_name = 'mdl_password';
    private $controller = 'password';
    private $settings = [
        'permissions'=> ['add', 'edit', 'remove','download','upload'],
    ];
    
    private $scripts = ['doctor.js'];    

	function __construct() {
        $user_role = $this->session->get_field_from_session('role');

        /* $this->settings = in_array(strtoupper($user_role), ['SA', 'A']) ? [
            'permissions'=> ['download'],
        ] : $this->settings; */
        
        parent::__construct( 
            $this->module, 
            $this->controller, 
            $this->model_name,
            $this->settings,    
            $this->scripts,
            ['jCrop','fancybox']
        );

        $this->set_defaults();
    }

	function options(){
		$this->session->is_Ajax_and_logged_in();

		$limit = $this->dropdownlength;
		$page = intval($_POST['page']) - 1;
		$page = ($page <= 0) ? 0 : $page;

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';
		$id = (isset($_POST['id'])) ? (int) $this->input->post('id') : 0;
        $filters = array();
		if($id){ $filters['code'] = $id; }

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'code', $filters, $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'code', $filters));

		foreach($_options as $option){
			$new['id'] = $option->id;
			$new['text'] = $option->code;

			array_push($json['results'], $new);
		}
		
		$more = ($_opt_count > count($_options)) ? TRUE : FALSE;
		$json['pagination']['more'] = $more;

		echo json_encode($json);
    }

	function redirect(){

		$key = $this->input->get('id', TRUE);
		
		if(empty($key)){
			show_404();
		}

		$user_record = $this->model->get_records(['users_emp_id'=> $key], 'manpower', [], '', 1);
		$data['users_id'] = $users_id = $user_record[0]->users_id;
		
		if(empty($users_id)){
			show_404();
		}
		
		$feedback_records = $this->model->get_records(['doctor_id'=> $doctor_id, 'complete_status' => 1], 'feedback', [], '', 1);
		$data['id'] = $id = $feedback_records[0]->doctor_id;

		/* if(!empty($id)){
			//$this->set_view('submitted');
			$this->load->view('submitted');
			return;
		} */

		$this->set_view($data, 'index');
	}

	function thank_you(){
		$this->load->view('thank-you');
	}

	function save(){
		$result = $this->model->save();
		echo json_encode($result);
	}

}
