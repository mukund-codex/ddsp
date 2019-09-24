<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class City extends Admin_Controller
{
	private $module = 'city';
    private $model_name = 'mdl_city';
    private $controller = 'city';
    private $settings = [
        'permissions'=> ['add', 'edit', 'remove', 'upload', 'download'],
    ];
    
    private $scripts = ['load-geography.js'];    

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

		if($id){ $filters['region_id'] = $id; }

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'area_name', $filters, $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'area_name', $filters));

		foreach($_options as $option){
			$new['id'] = $option->area_id;
			$new['text'] = $option->area_name;

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

			if(count($data) !== 2) { continue; }

			if(! $cnt){
                $cnt++; continue;
            }

			$state = trim($data[0]);
			$city = trim($data[1]);

			if(empty($state) || empty($city)){
				continue;
			}

			/* if(!ctype_alnum($state) || !ctype_alnum($city)){
				continue;
			} */

			$state_records = $this->model->get_records(['state'=>$state], 'state', ['id'], '', 1);
			if(empty($state_records)){
				continue;
			}

			$state_id = $state_records[0]->id;

			$insert['state_id'] = $state_id;
			$insert['city_name'] = $city;

			$this->model->_insert($insert);
			$newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
