<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Communication extends User_Controller
{
	private $module = 'communication';
    private $model_name = 'mdl_communication';
    private $controller = 'communication';
    private $settings = [
        'permissions'=> ['add','edit','remove','download'],
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
            ['fancybox']
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

		$_options = $this->model->get_options($s_term, 'brand_name', $filters, $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'brand_name', $filters));

		foreach($_options as $option){
			$new['id'] = $option->brand_id;
			$new['text'] = $option->brand_name;

			array_push($json['results'], $new);
		}
		
		$more = ($_opt_count > count($_options)) ? TRUE : FALSE;
		$json['pagination']['more'] = $more;

		echo json_encode($json);
    }

	function whatsapp(){
		$this->session->is_Ajax_and_logged_in();

		$response = $this->model->whatsapp();
		echo json_encode($response);
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

			if(count($data) !== 2) { continue; }            
			
			$molecule_name = trim($data[0]);

			$molecule = $this->model->get_or_records(['molecule_name'=> $molecule_name], 'molecule', ['molecule_id'], '', 1);

			if(empty($molecule)){ continue; }

			$molecule_id = $molecule[0]->molecule_id;

            $brand_name = trim($data[1]);

            if( empty($brand_name)){
                continue;
            }

			$record = $this->model->get_or_records(['brand_name'=> $brand_name], 'brand', ['brand_id'], '', 1);
			if(count($record)) {
				continue;
			}

			$insert['molecule_id'] = $molecule_id;
            $insert['brand_name'] = $brand_name;

            $this->model->_insert($insert);

            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
