<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Zone extends Admin_Controller
{
	private $module = 'zone';
    private $model_name = 'mdl_zone';
    private $controller = 'geography/zone';
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

		$_options = $this->model->get_options($s_term, 'zone_name', [], $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'zone_name'));

		foreach($_options as $option){
			$new['id'] = $option->zone_id;
			$new['text'] = $option->zone_name;

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

			if(count($data) !== 1) { continue; }

			if(! $cnt){
                $cnt++; continue;
            }
            
            $zone_name = trim($data[0]);

            if( empty($zone_name) ){
                continue;
            }

            if( ! preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $zone_name) ){
                continue;
            }

            $z_records = $this->model->get_records([ 'zone_name'=> $zone_name ], 'zone', ['zone_id', 'zone_name'], 'zone_id', 1);

            if(count($z_records)){
                continue;
            }

            $insert['zone_name'] = $zone_name;
            $this->model->_insert($insert);

            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
