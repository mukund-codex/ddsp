<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Users extends Generic_Controller
{
	private $module = 'users';
	private $model_name = 'mdl_users';
	private $csv_fields;
	private $type_array;

	function __construct() {
		parent::__construct();

		$this->data['mainmenu'] = 'manpower';
		$this->data['controller'] = $this->data['menu'] = $this->data['m_title'] = $this->module;
		$this->data['columns'] = ['Users Name'];

		$this->type_array = ['zsm', 'asm','mr'];
		$this->load->model($this->model_name, 'model');
	}

	function _get_heirarchy_through_city($city_id){
		$this->load->model('users/mdl_users', 'modelusers');
		$result = $this->modelusers->get_heirarchy_through_city($city_id);
		$result = array_filter($result);
		return $result;
	}

	private function user_data($filters = []){

		if(count($filters)){
			return $this->model->get_user_info($filters);	
		}

		return [];
	}

	/*getting user info of particular user: called from javscript - load-manpower.js*/
	function user_info(){
		$this->session->is_Ajax_and_logged_in();

		if(isset($_POST['id'])){
			
			$id = intval($_POST['id']);
			$collection = $this->user_data(['users_id'=> $id]);

			$response = (count($collection)) ? $collection[0] : [];
			echo json_encode($response);

		}
		else{
			echo json_encode(['error'=>'Unauthorized Access']);
		}
	}

	function options(){
		$this->session->is_Ajax_and_logged_in();

		$page = intval($_POST['page']) - 1;
		$page = ($page <= 0) ? 0 : $page;
		$new = array(); $json['results'] = array();

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';
		$role = $this->input->post('role');

		if(! in_array($role, $this->type_array)){
			echo json_encode($json['results']); exit;
		}
		
		$filters = ['users_type'=> strtoupper($role) ];

		echo '<pre>';print_r($filters);exit;

		$_options = $this->model->get_user_options($s_term, 'users_name', $filters, $page * $this->dropdownlength, $this->dropdownlength);
		$_opt_count = count($this->model->get_user_options($s_term, 'users_name', $filters ));

		if(! count($_options)){
			echo json_encode($json['results']); exit;
		}

		foreach($_options as $option){
			$new['id'] = $option->users_id;
			$user_type_info = strtoupper($option->users_type);
			if($user_type_info == 'HO'){
				$new['text'] = $option->users_name;	
			}

			if($user_type_info == 'ZSM'){
				$new['text'] = $option->users_name . ' -- ' . $option->zone_name;	
			}
			
			if($user_type_info == 'RSM'){
				$new['text'] = $option->users_name . ' -- ' . $option->region_name . ' -- ' . $option->zone_name;	
			}
			
			if($user_type_info == 'ASM'){
				$new['text'] = $option->users_name . ' -- ' . $option->area_name . ' -- ' . $option->region_name . ' -- ' . $option->zone_name;	
			}

			if($user_type_info == 'MR'){
				$new['text'] = $option->users_name . ' -- ' . $option->city_name . ' -- '. $option->area_name . ' -- ' . $option->region_name . ' -- ' . $option->zone_name;	
			}
			
			$new['zone'] = $option->users_zone_id;
			$new['area'] = $option->users_area_id;
			$new['city'] = $option->users_city_id;
			$new['a_name'] = $option->area_name;

			array_push($json['results'], $new);
		}

		$more = ($_opt_count > count($_options)) ? TRUE : FALSE;
		$json['pagination']['more'] = $more;

		echo json_encode($json);
	}

	function lists(){
		
		if( ! $this->session->is_logged_in() ){
			redirect('admin/login','refresh');
		}

		$array = $this->uri->uri_to_assoc();
		$user_type = (array_key_exists('all', $array)) ? $array['all'] : '';

		if(! in_array($user_type, $this->type_array)){
			show_404();
		}

		$sfilters = array('users_type'=> $user_type);

		$users_name = strtoupper($user_type).' Name'; 
		$users_mobile = strtoupper($user_type).' Mobile';
		$csv_fields = [$users_name, $users_mobile];

		$this->data['users_type'] = $user_type;

		if($user_type == 'ho'){
			array_push($csv_fields, 'Password');
			$this->data['menu'] = 'ho';
			$this->data['columns'] = ['HO Name', 'HO Mobile', 'HO Password'];
		}

		if($user_type == 'zsm'){
			array_push($csv_fields, 'Zone');		
			$this->data['menu'] = 'zsm';

			$this->data['columns'] = ['ZSM Name', 'ZSM Mobile', 'Zone'];
		}

		if($user_type == 'rsm'){
			array_push($csv_fields, 'Region', 'ZSM Name');		
			$this->data['menu'] = 'rsm';

			$this->data['columns'] = ['RSM Name', 'RSM Mobile', 'Region', 'ZSM Name', 'Zone'];
		}

		if($user_type == 'asm'){
			array_push($csv_fields, 'Area', 'RSM Name');		
			$this->data['menu'] = 'asm';

			$this->data['columns'] = ['ASM Name', 'ASM Mobile', 'Area', 'RSM Name', 'Region'];
		}

		if($user_type == 'mr'){
			array_push($csv_fields, 'City', 'ASM Name');		
			$this->data['menu'] = 'mr';

			$this->data['columns'] = ['MR Name', 'MR Mobile', 'City', 'ASM Name', 'Area'];
		}

		$page = $this->input->post('page');
        $offset = (!$page) ? 0 : intval($page);

		$keywords = !empty($this->input->post('keywords'))?$this->input->post('keywords'):'';

		if (!empty($keywords)) {
			$this->data['collection'] = $this->model->get_collection($sfilters, $keywords, $this->perPage);
		} else {
			$this->data['collection'] = $this->model->get_collection($sfilters, $keywords, $this->perPage, $page);
		}

		$totalRec = count($this->model->get_collection($sfilters, $keywords));
		$this->paginate($this->data['controller'] . '/view/all/' . $user_type, $totalRec, 5);

        $this->data['csv_fields'] = $csv_fields;
		$this->data['plugins'] = ['paginate'];
		
		$this->data['listing_url'] = $this->data['controller'] . '/lists/all/' . $user_type;
		$this->data['download_url'] = $this->data['controller'] . '/download/type/' . $user_type;

        $title_txt = 'Manage '. strtoupper($user_type);

        if ($this->input->post('search') == TRUE) {
        	$this->load->view($this->data['controller'].'/results', $this->data);
        }else
        {
			$this->set_view($this->data, 'lists',  '_admin', $title_txt);
		}
	}


	function add(){
		if( ! $this->session->is_logged_in() )
			redirect('admin/login','refresh');

		$array = $this->uri->uri_to_assoc();
		$user_type = (array_key_exists('type', $array)) ? $array['type'] : '';

		if(! in_array($user_type, $this->type_array)){
			show_404();
		}

		$this->data['menu'] = $this->data['u_type'] = $user_type;

		$this->data['plugins'] = ['select2'];
		$this->data['js']	= ['generic-add.js', 'load-manpower.js'];

		$viewFile = 'add-' . $user_type;
		$title_txt = 'Add '. strtoupper($user_type);

		$this->set_view($this->data, $viewFile, '_admin', $title_txt);
	}

	function edit(){
		if( ! $this->session->is_logged_in('A') )
			redirect('admin/login','refresh');

		$array = $this->uri->uri_to_assoc();
		
		/*check for valid user type*/			
		$user_type = (array_key_exists('type', $array)) ? $array['type'] : '';
		if(! in_array($user_type, $this->type_array)){
			show_404();
		}

		/*check for whether users_id is available and is numeric*/
		$this->data['users_id'] = $user_id = (array_key_exists('record', $array)) ? (int) $array['record'] : '';

		/*check for a valid user*/
		$this->data['info'] = $this->model->get_collection( array('users_id'=> $user_id) );
		if(! count($this->data['info'])){ show_404(); }

		$this->data['menu'] = $this->data['u_type'] = $user_type;
		
		$this->data['plugins'] = ['select2'];
		$this->data['js']	= ['generic-edit.js', 'load-manpower.js'];

		$viewFile = 'edit-' . $user_type;
		$title_txt = 'Edit '. strtoupper($user_type);

		$this->set_view($this->data, $viewFile, '_admin', $title_txt);
	}

	function save(){
		$this->session->is_Ajax_and_logged_in();

		$result = $this->model->save();
		echo json_encode($result);
	}

	function modify(){
		$this->session->is_Ajax_and_logged_in();

		$result = $this->model->modify();
		echo json_encode($result);
	}

	function remove(){
		$this->session->is_Ajax_and_logged_in();

		$response = $this->model->remove();
		echo json_encode($response);
	}

	function download(){

		if( ! $this->session->is_logged_in() )
			redirect('admin/login','refresh');

		$array = $this->uri->uri_to_assoc();
		$user_type = (array_key_exists('type', $array)) ? $array['type'] : '';

		if(! in_array($user_type, $this->type_array)){ show_404(); }

		$keywords = (isset($_GET['keywords'])) ? $this->db->escape_like_str($_GET['keywords']) : '';

		$data = $this->model->get_collection(['users_type'=> strtoupper($user_type) ], $keywords);
		$fields = $this->model->_format_data_to_export($data, $user_type);

		$this->download_file($user_type . '-' . date('Y-m-d'), $fields);
	}

	private function arrayMapRecursive($callback, $array) {
		$func = function ($item) use (&$func, &$callback) {
			return is_array ($item) ? array_map ($func, $item) : call_user_func ($callback, $item);
		};
		
		return array_map ($func, $array);
	}

	function uploadcsv(){
		$this->session->is_Ajax_and_logged_in();

		/*upload csv file */
		
		if(! is_uploaded_file($_FILES['csvfile']['tmp_name'])){
			echo json_encode(['errors'=> ['csvfile'=> '<label class="error">Please Upload CSV file</label>']]); exit;
		}

		if(!in_array($_FILES['csvfile']['type'], array('application/vnd.ms-excel', 'application/csv', 'text/csv')) ){
			echo json_encode(['errors'=> ['csvfile'=> '<label class="error">Only .CSV files allowed</label>']]); exit;
		}
		
		$u_type = (isset($_POST['users_type'])) ? $this->input->post('users_type') : ''; 

		if(! in_array($u_type, $this->type_array)){
			echo json_encode(['errors'=> ['csvfile'=> '<label class="error">User type invalid, please contact support staff</label>']]); exit;
		}

		$file = $_FILES['csvfile']['tmp_name'];
		$handle = fopen($file, "r");	 
		$cnt = 0; $newrows = 0;
		
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
			$data = $this->arrayMapRecursive('trim', $data);

			if($cnt != 0){
				$insert_flag = FALSE;
			
				$users_name = $data[0];
				$users_mobile = $data[1];
				
				if($u_type == 'ho'){
					$users_password = $data[2];
				}

				if($u_type == 'zsm'){
					$zone = $data[2];
				}

				if($u_type == 'rsm'){
					$region = $data[2];
					$zsm_name = $data[3];
				}

				if($u_type == 'asm'){
					$area = $data[2];
					$rsm_name = $data[3];
				}

				if($u_type == 'mr'){
					$city = $data[2];
					$asm_name = preg_replace('/[^A-Za-z0-9\-\s]/', '', $data[3]);
				}

				$i_record['users_name'] = $users_name;
				$i_record['users_mobile'] = $users_mobile;
				$i_record['users_type'] = strtoupper($u_type);
				
				if($u_type == 'ho'){
					$i_record['users_password'] = $users_password;
				}

				$row_no = $cnt + 1;

				if($u_type != 'mr'){
					$mobile_err = (preg_match('/^[1-9][0-9]{0,9}$/', $users_mobile)) ? TRUE : FALSE;
					if(! $mobile_err){
						continue;
					}
				}
				
				$record = $this->model->get_records(['users_mobile'=> $users_mobile , 'users_type' => strtoupper($u_type)]);
				
				if( count($record) ){
					continue;
				}

				if( $u_type == 'ho' ){
					$ho_added = $this->model->_insert($i_record);
					if( $ho_added ){ $newrows++; }
					continue;
				}
				else{
					
					if($u_type == 'zsm'){

						$zone_exists = $this->model->get_records(['zone_name'=> $zone], 'zone');

						if(! count($zone_exists)){
							continue;
						}
						
						$i_record['users_zone_id'] = $filters['users_zone_id'] = $zone_exists[0]->zone_id;
					}

					if($u_type == 'rsm'){
						$zsm_exists = $this->model->get_records(['users_name'=> $zsm_name, 'users_type'=> 'ZSM']);

						if(! count($zsm_exists)){
							continue;
						}

						$zsm_zone_id = $zsm_exists[0]->users_zone_id;

						$region_exists = $this->model->get_records(['region_name'=> $region, 'zone_id'=> $zsm_zone_id], 'region');

						if( ! count($region_exists) ){
							continue;
						}
						
						$i_record['users_parent_id'] = $zsm_exists[0]->users_id;
						$i_record['users_zone_id'] = $region_exists[0]->zone_id;
						$i_record['users_region_id'] = $filters['users_region_id'] = $region_exists[0]->region_id;
						
						$filters['users_region_id'] = $region_exists[0]->region_id;
						//$filters['users_parent_id'] = $zsm_exists[0]->users_id;
					}
					
					if($u_type == 'asm'){

						$rsm_exists = $this->model->get_records(['users_name'=> $rsm_name, 'users_type'=> 'RSM']);

						if(! count($rsm_exists)){
							continue;
						}

						$rsm_region_id = $rsm_exists[0]->users_region_id;

						$area_exists = $this->model->get_records(['area_name'=> $area, 'region_id'=> $rsm_region_id], 'area');

						if( ! count($area_exists) ){
							continue;
						}

						$i_record['users_zone_id'] = $rsm_exists[0]->users_zone_id;
						$i_record['users_region_id'] = $area_exists[0]->region_id;
						$i_record['users_area_id'] = $filters['users_area_id'] = $area_exists[0]->area_id;
						$i_record['users_parent_id'] = $rsm_exists[0]->users_id;
						$filters['users_area_id'] = $area_exists[0]->area_id;
					}

					if($u_type == 'mr'){
						
						$asm_exists = $this->model->get_records(['users_name'=> trim($asm_name), 'users_type'=> 'ASM']);

						if(! count($asm_exists)){
							continue;
						}
	
						$asm_area_id = $asm_exists[0]->users_area_id;
	
						$city_exists = $this->model->get_records(['city_name'=> $city, 'area_id'=> $asm_area_id], 'city');

						if( ! count($city_exists) ){
							continue;
						}					

	
						$i_record['users_zone_id'] = $asm_exists[0]->users_zone_id;
						$i_record['users_region_id'] = $asm_exists[0]->users_region_id;
						$i_record['users_area_id'] = $asm_area_id;
						$i_record['users_city_id'] = $city_exists[0]->city_id;
						$i_record['users_parent_id'] = $asm_exists[0]->users_id;
	
						$filters['users_city_id'] = $city_exists[0]->city_id;
						//$filters['users_parent_id'] = $rsm_exists[0]->users_id;
					}

					if($u_type != 'ho'){
						$filters['users_type'] = strtoupper($u_type);
						$is_already_available = $this->model->get_records($filters);

						if( count($is_already_available) ){
							continue;
						}
					}
					$users_id = $this->model->_insert($i_record);
					
					$newrows++;	
				}
			}
			$cnt++;	
		}

		fclose($handle);

		$resultant_arr['newrows'] = "$newrows record(s) added successfully";
		echo json_encode($resultant_arr);
	}
}
