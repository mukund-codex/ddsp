<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Asm extends Admin_Controller
{
    private $module = 'asm';
    private $model_name = 'mdl_asm';
    private $controller = 'manpower/asm';
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

	private function user_data($filters = []){

		if(count($filters)){
			return $this->model->get_user_info($filters);	
		}

		return [];
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

			if(count($data) !== 7) { continue; }

			if(! $cnt){
                $cnt++; continue;
            }
            
            $asm_name = trim($data[0]);
            $mobile = trim($data[1]);
            $emp_id = trim($data[2]);
            $password = trim($data[3]);
            $area = trim($data[4]);
            $region = trim($data[5]);
            $zone = trim($data[6]);

			if( empty($asm_name) || empty($emp_id) || 
				empty($password) || empty($area) || empty($region) || empty($zone) ){
                continue;
            }

            if( 
                ! preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $asm_name) 
                || ! preg_match('/^[a-zA-Z0-9]+$/', $emp_id) 
                || ! preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $area) 
                || ! preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $region) 
                || ! preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $zone) 
            ){
                continue;
            }
            
            if(!empty($mobile)) {
                if(! preg_match('/^[1-9][0-9]{9}$/', $mobile)) {
                    continue;
                }
            } else {
                $mobile = NULL;
            }

			$zone_record = $this->model->get_records(['zone_name'=> $zone], 'zone', ['zone_id'], '', 1);
            if(!count($zone_record)) {
                continue;
            }
            
			$zone_id = $zone_record[0]->zone_id;
			
			$region_record = $this->model->get_records(['region_name'=> $region, 'zone_id' => $zone_id], 'region', ['region_id'], '', 1);
            if(!count($region_record)) {
                continue;
            }
            
            $region_id = $region_record[0]->region_id;
			
			$area_record = $this->model->get_records(['area_name'=> $area, 'region_id' => $region_id], 'area', ['area_id'], '', 1);
            if(!count($area_record)) {
                continue;
            }
            
            $area_id = $area_record[0]->area_id;
            
            $record = $this->model->get_collection(TRUE, ['zone_name' => $zone, 'area_name'=> $area, 'region_name' => $region]);
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
			
			$user_parent_data = $this->model->get_records([
				'users_zone_id' => $zone_id, 
				'users_region_id' => $region_id, 
				'users_type' => 'RSM'
			], 'manpower', ['users_id'], 1);

			if(! $user_parent_data) {
				continue;
			}

			$user_parent_id = $user_parent_data[0]->users_id;

            $insert['users_name'] = $asm_name;
 
            if($mobile) {
                $insert['users_mobile'] = $mobile;
            }

            $insert['users_emp_id'] = $emp_id;
            $insert['users_password'] = $password;
            $insert['users_zone_id'] = $zone_id;
			$insert['users_region_id'] = $region_id;
			$insert['users_area_id'] = $area_id;
			$insert['users_parent_id'] = $user_parent_id;
			$insert['users_type'] = "ASM";

            $this->model->_insert($insert);

            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
