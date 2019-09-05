<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller
{
	private $module = 'admin';
    private $model_name = 'mdl_admin';
    private $controller = 'admin';
    private $settings = [
        'permissions'=> ['add', 'edit', 'download', 'upload', 'remove'],
    ];

	function __construct() {
        parent::__construct( 
            $this->module, 
            $this->controller, 
            $this->model_name,
            $this->settings,
            ['admin/user.js']
        );
        $this->set_defaults();
	}
    
    function options_role(){
		$this->session->is_Ajax_and_admin_logged_in();

		$limit = 15;
		$page = intval($_POST['page']) - 1;
		$page = ($page <= 0) ? 0 : $page;

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';
		$id = (isset($_POST['id'])) ? (int) $this->input->post('id') : 0;

		if($id){
			$filters['role_id'] = $id;
		}else{
			$filters = [];
		}

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'label', $filters, $page * $limit, $limit, [], 'roles');
		$_opt_count = count($this->model->get_options($s_term, 'label', $filters, 0, 0, [], 'roles'));

		foreach($_options as $option){
			$new['id'] = $option->role_id;
			$new['text'] = $option->label;

			array_push($json['results'], $new);
		}
		
		$more = ($_opt_count > count($_options)) ? TRUE : FALSE;
		$json['pagination']['more'] = $more;

		echo json_encode($json);
    }

	function uploadcsv(){
		$this->session->is_Ajax_and_admin_logged_in();
		/*upload csv file */

		if(! is_uploaded_file($_FILES['csvfile']['tmp_name'])){
			echo json_encode(['errors'=> ['csvfile'=> '<label class="error">Please Upload CSV file</label>']]); exit;
		}

		if(!in_array($_FILES['csvfile']['type'], array('application/vnd.ms-excel', 'application/csv', 'text/csv')) ){
			echo json_encode(['errors'=> ['csvfile'=> '<label class="error">Only .CSV files allowed</label>']]); exit;
		}

		$file = $_FILES['csvfile']['tmp_name'];
		$handle = fopen($file, "r");
		$cnt = 0; $newrows = 0;

		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){

			if($cnt != 0){
				$user_name = trim($data[0]);
				$password = trim($data[1]);
				$user_type = trim($data[2]);
				$full_name = trim($data[3]);

				if( empty($user_name) || empty($password) || empty($user_type) || preg_match('/[^a-z0-9]/i', $user_name)){
					continue;
				}

				$username_record = $this->model->get_records(['username'=> $user_name], 'admin', ['user_id']);
				if( count($username_record) ){ continue; }

				$user_type_record = $this->model->get_records(['label'=> $user_type], 'roles', ['role_id', 'role'], 'label', 1);
				if( ! count($user_type_record) ) { continue; }

				$insert['username'] = $user_name;
				$insert['password'] = $password;
				$insert['user_type'] = $user_type_record[0]->role_id;
				$insert['password'] = $password;

				if(!empty($full_name)){
					$insert['full_name'] = $full_name;
				}

				$this->model->_insert($insert);
				$newrows++;
			}

			$cnt++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}