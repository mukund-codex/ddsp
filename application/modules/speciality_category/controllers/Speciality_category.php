<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Speciality_category extends Admin_Controller
{
	private $module = 'speciality_category';
    private $model_name = 'mdl_speciality_category';
    private $controller = 'speciality_category';
    private $settings = [
        'permissions'=> ['add','edit','remove','download','upload'],
    ];
    
    private $scripts = ['doctor.js'];    

	function __construct() {
        $user_role = $this->session->get_field_from_session('role');
        
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

		$filters = [];

		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';
		$id = (isset($_POST['id'])) ? (int) $this->input->post('id') : 0;

		if($id){ $filters['region_id'] = $id; }

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'molecule_name', $filters, $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'molecule_name', $filters));

		foreach($_options as $option){
			$new['id'] = $option->molecule_id;
			$new['text'] = $option->molecule_name;

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
            
			if(! $cnt){
                $cnt++; continue;
            }

			if(count($data) !== 3) { continue; }            
			
			$speciality_name = trim($data[0]);			
            $category_name = trim($data[1]);
			$category_type = trim($data[2]);
		
			if( empty($speciality_name)){
                continue;
			}

			if( empty($category_name)){
                continue;
			}

			if( empty($category_type)){
                continue;
			}
			
			if($category_type != 'rxn' && $category_type != 'strips'){
				continue;
			}
			
			$specrecord = $this->model->get_or_records(['speciality_name'=> $speciality_name], 'speciality', ['speciality_id'], '', 1);
			if(empty($specrecord)) {
				continue;
			}
			
			$speciality_id = $specrecord[0]->speciality_id;

			$record = $this->model->get_or_records(['category_name'=> $category_name], 'speciality_category', ['sc_id'], '', 1);
			if(count($record)) {
				continue;
			}

			$insert['speciality_id'] = $speciality_id;
            $insert['category_name'] = $category_name;
			$insert['category_type'] = $category_type;

            $this->model->_insert($insert);

            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
