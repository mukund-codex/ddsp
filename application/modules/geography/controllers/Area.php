<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Area extends Admin_Controller
{
	private $module = 'area';
    private $model_name = 'mdl_area';
    private $controller = 'geography/area';
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
        $filters = [];
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

            $zone_name = trim($data[0]);
            $area_name = trim($data[1]);

            if( empty($zone_name) || empty($area_name)){
                continue;
            }

            if( 
                !preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $zone_name)  
                || !preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $area_name) ){
                continue;
            }

            $zone = $this->model->get_records(['zone_name'=> $zone_name], 'zone');
            if(! count($zone)) {
                continue;
            }

            $zone_id = $zone[0]->zone_id;

            $record = $this->model->get_collection(
                FALSE, 
                [ 'zone_name'=> $zone_name, 'area_name'=> $area_name ], 
                [], 
                1
            );

            if( count($record)){
                continue;
            }

            $insert['zone_id'] = $zone_id;
            $insert['area_name'] = $area_name;

            $this->model->_insert($insert);
            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
