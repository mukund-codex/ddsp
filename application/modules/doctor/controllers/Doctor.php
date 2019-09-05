<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Doctor extends User_Controller
{
	private $module = 'doctor';
    private $model_name = 'mdl_doctor';
    private $controller = 'doctor';
    private $settings = [
        'permissions'=> ['add','edit','remove','download'],
    ];
    
    private $scripts = ['doctor.js'];    

	function __construct() {
        $user_role = $this->session->get_field_from_session('role');

        /* $this->settings = in_array(strtoupper($user_role), ['SA', 'A']) ? [
            'permissions'=> [],
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

    function preview(){
        $this->session->is_Ajax_and_logged_in();
        
		$result = $this->model->preview();
		echo json_encode($result);
	}
    
    function upload() {
        $this->load->helper('upload_media');
        $response = upload_media('doctor_photo', 'uploads/doctors', ['jpg', 'png', 'jpeg'], 200000);
        
        if(array_key_exists('status', $response)) {
            echo json_encode(['status'=> FALSE, 'error' => ['doctor_photo' => '<label class="error">'.$response['error']. '</label>']]); die();
        }

        echo json_encode([
            'status'=> TRUE, 
            'path'=> base_url($response[0]['file_name']), 
            'filename'=> $response[0]['raw_name'] . $response[0]['file_ext']
        ]); die();
    }

    function crop() {
        //print_r($_POST); die();
        $this->load->library('Image');

        $image = $this->input->post('img');
        
        $top_x = $this->input->post('x_axis');
        $top_y = $this->input->post('y_axis');
        $bottom_x = $this->input->post('thumb_width');
        $bottom_y = $this->input->post('thumb_height');
        
        $imageObj = new Image("uploads/doctors/$image");

        $imageObj->crop($top_x, $top_y, $bottom_x, $bottom_y);
        $imageObj->save('uploads/doctors/newfile.jpg');
        print_r($imageObj); die();
    }

    function download(){
		$this->session->is_Ajax_and_logged_in();

		$response = $this->model->download();
		echo json_encode($response);
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

			if(count($data) !== 3) { continue; }

			if(! $cnt){
                $cnt++; continue;
			}

            $zone_name = trim($data[0]);
            $region_name = trim($data[1]);
            $area_name = trim($data[2]);

            if( empty($zone_name) || empty($region_name)){
                continue;
            }

            if( 
                !preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $zone_name)  
                || !preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $region_name)
                || !preg_match('/^[a-zA-Z][a-zA-Z0-9 \.]+$/', $area_name) ){
                continue;
            }

            $zone = $this->model->get_records(['zone_name'=> $zone_name], 'zone');
            if(! count($zone)) {
                continue;
            }

            $zone_id = $zone[0]->zone_id;

            $region = $this->model->get_records(['region_name'=> $region_name, 'zone_id'=> $zone_id], 'region');
            if(! count($region)) {
                continue;
            }

            $record = $this->model->get_collection(
                FALSE, 
                [ 'zone_name'=> $zone_name, 'region_name'=> $region_name, 'area_name'=> $area_name ], 
                '', 
                1
            );

            if( count($record)){
                continue;
            }

            $insert['region_id'] = $region[0]->region_id;
            $insert['area_name'] = $area_name;

            $this->model->_insert($insert);
            $newrows++;
		}

		fclose($handle);

		echo json_encode(['newrows'=> "$newrows record(s) added successfully"]);
	}
}
