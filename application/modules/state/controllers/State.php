<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class State extends Admin_Controller
{
	private $module = 'state';
    private $model_name = 'mdl_state';
    private $controller = 'state';
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

		//$id = (isset($_POST['id'])) ? (int) $this->input->post('id') : 0;
        $filters = array();
		//if($id){ $filters['state'] = $id; }
		$s_term = (isset($_POST['search'])) ? $this->db->escape_like_str($_POST['search']) : '';

		$new = array(); $json['results'] = array();

		$_options = $this->model->get_options($s_term, 'state', $filters, $page * $limit, $limit);
		$_opt_count = count($this->model->get_options($s_term, 'state', $filters));
		
		foreach($_options as $option){
			$new['id'] = $option->id;
			$new['text'] = $option->state;

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

			$state = trim($data[0]);
			
			if(empty($state)){
				continue;
			}

			$state_record = $this->model->get_records(['state'=> $state], 'state', ['id'], '', 1);
			
			if(!empty($state_record)){
				continue;
			}

			$insert['state'] = $state;

			$this->model->_insert($insert);
			$newrows++;
            
		}
		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
