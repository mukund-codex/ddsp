<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Nsm extends Admin_Controller
{
	private $module = 'nsm';
    private $model_name = 'mdl_nsm';
    private $controller = 'manpower/nsm';
    private $settings = [
        'permissions'=> ['add', 'edit', 'download', 'upload', 'remove'],
        'paginate_index' => 4
    ];
    private $scripts = ['load-geography.js'];

	function __construct() {
        parent::__construct( 
            $this->module, 
            $this->controller, 
            $this->model_name,
            $this->settings,
            $this->scripts
        );

        $this->set_defaults();
    }
  
	function options(){
		$this->session->is_Ajax_and_logged_in();

		$limit = $this->dropdownlength;
		$page = intval($_POST['page']) - 1;
		$page = ($page <= 0) ? 0 : $page;

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'users_name', [], $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'users_name'));

		foreach($_options as $option){
			$new['id'] = $option->users_id;
			$new['text'] = $option->users_name;

			array_push($json['results'], $new);
		}
		
		$more = ($_opt_count > count($_options)) ? TRUE : FALSE;
		$json['pagination']['more'] = $more;

		echo json_encode($json);
    }
    
    function options_data(){
		$this->session->is_Ajax_and_logged_in();

		$limit = $this->dropdownlength;
		$page = intval($_POST['page']) - 1;
		$page = ($page <= 0) ? 0 : $page;

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';

		$new = array(); $json['results'] = array();

        $_options = $this->model->get_records(['users_type' => 'NSM'], 'manpower', ['DISTINCT(users_id)','users_name']);
        $_opt_count = count($_options);

		foreach($_options as $option){
			$new['id'] = $option->users_id;
			$new['text'] = $option->users_name;

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

			if(count($data) !== 5) { continue; }

			if(! $cnt){
                $cnt++; continue;
            }
            
            $nsm_name = trim($data[0]);
            $mobile = trim($data[1]);
            $emp_id = trim($data[2]);
            $password = trim($data[3]);
            $national_zone = trim($data[4]);

            if( empty($nsm_name) || empty($emp_id) || empty($password) || empty($national_zone) ){
                continue;
            }

            if( 
                ! preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $nsm_name) 
                || ! preg_match('/^[a-zA-Z0-9]+$/', $emp_id) 
                || ! preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $national_zone) 
            ){
                continue;
            }
            
            if(empty($mobile) || !is_numeric($mobile) || strlen($mobile) > 10){
                continue;
            }
			
			$national_zone_record = $this->model->get_records(['national_zone_name'=> $national_zone], 'national_zone', ['national_zone_id'], '', 1);
            if(!count($national_zone_record)) {
                continue;
            }
            
            $national_zone_id = $national_zone_record[0]->national_zone_id;
            
            $record = $this->model->get_collection(TRUE, ['national_zone_name'=> $national_zone]);
            if($record) {
                continue;
            }

            if($mobile) {
                $emp_record = $this->model->get_or_records(
                    [ 
                        'users_mobile'=> $mobile,
                        'users_emp_id'=> $emp_id,
                    ], 'manpower', ['users_id'], '', 1
                );
            } else {
                $emp_record = $this->model->get_records(
                    [ 'users_emp_id'=> $emp_id ],
                    'manpower',
                    ['users_id'],
                    '',
                    1
                );
            }

            if(count($emp_record)) {
                continue;
            }

            $insert['users_name'] = $nsm_name;
            
            if($mobile) {
                $insert['users_mobile'] = $mobile;
            }

            $insert['users_emp_id'] = $emp_id;
            $insert['users_password'] = $password;
            $insert['users_national_id'] = $national_zone_id;
            $insert['users_type'] = "NSM";

            $this->model->_insert($insert);

            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
