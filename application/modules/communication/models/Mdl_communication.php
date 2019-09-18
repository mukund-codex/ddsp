<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_communication extends MY_Model {

	private $p_key = 'c_id';
	private $table = 'communication';
	private $alias = 'c';
	private $fillable = ['title','description', 'images, document, selected_roles[], group_id'];
    private $column_list = ['Title', 'Description', 'Media','Date'];
    private $csv_columns = [];

	function __construct() {
        parent::__construct($this->table, $this->p_key,$this->alias);
    }
    
    function get_csv_columns() {
        return $this->csv_columns;
    }

    function get_column_list() {
        return $this->column_list;
    }

    function get_filters() {
        return [
            [
                'field_name'=>'title',
                'field_label'=> 'Title',
			]
        ];
    }

    function get_filters_from($filters) {
        $new_filters = array_column($this->get_filters(), 'field_name');
        
        if(array_key_exists('from_date', $filters))  {
            array_push($new_filters, 'from_date');
        }

        if(array_key_exists('to_date', $filters))  {
            array_push($new_filters, 'to_date');
        }

        return $new_filters;
    }

	function get_collection( $count = FALSE, $sfilters = [], $rfilters = [], $limit = 0, $offset = 0, ...$params ) {
        
        $q = $this->db->select('c.c_id, c.title, c.description, c.insert_dt, c.update_dt, GROUP_CONCAT(cm.media) as media')
		->from('communication c')
		->join('communication_media cm', 'cm.c_id = c.c_id', 'left');
        
		if(sizeof($sfilters)) { 
            
            foreach ($sfilters as $key=>$value) { 
                $q->where("$key", $value); 
			}
		}
        
		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                if(!in_array($key, $field_filters)) {
                    continue;
                }
                
                if($key == 'from_date' && !empty($value)) {
                    $this->db->where('DATE(c.insert_dt) >=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
                    $this->db->where('DATE(c.insert_dt) <=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if(!empty($value))
                    $this->db->like($key, $value);
            }
        }

		$user_role = $this->session->get_field_from_session('role','user');

        if(empty($user_role)) {
            $user_role = $this->session->get_field_from_session('role');
		}
		
		if(in_array($user_role, ['MR','ASM','RSM'])) {
			$q->where('insert_user_id', $this->session->get_field_from_session('user_id', 'user'));
		}

		if(! $count) {
			$q->group_by('c.c_id');
			$q->order_by('c.c_id desc');
		}

		if(!empty($limit)) { $q->limit($limit, $offset); }        
        //echo $this->db->get_compiled_select(); die();
        $collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		return $collection;
    }	
    
    function validate($type)
	{
		if($type == 'save') {
			return [
				[
					'field' => 'title',
					'label' => 'Title',
					'rules' => 'trim|required|max_length[100]|xss_clean'
				],
				[
					'field' => 'description',
					'label' => 'Description',
					'rules' => 'trim|max_length[1000]|xss_clean'
				]
			];
		}

		if($type == 'modify') {
			return [
				[
					'field' => 'title',
					'label' => 'Title',
					'rules' => 'trim|required|max_length[100]|xss_clean'
				],
				[
					'field' => 'description',
					'label' => 'Description',
					'rules' => 'trim|max_length[1000]|xss_clean'
				]                
			];
		}
    }

	function save(){
		
		$this->load->library('form_validation');
		$this->load->helper('upload_media');
		
		$this->form_validation->set_rules($this->validate('save'));
		
		if(! $this->form_validation->run()){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
				$errors[$key] = form_error($key, '<label class="error">', '</label>');
				
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}
		
		$media_files = $is_image_file_upload = $is_doc_file_upload = [];
		
		if(array_sum($_FILES['images']['size']) > 0) {			
			
			if(count($_FILES['images']) > 5){
				$response['errors'] = [
					"images[]" => 'Maximum 5 Files to be uploaded.'
				]; 
            	$response['status'] = FALSE;
            	
				return $response;
			}

			$is_image_file_upload = upload_media('images', 'uploads/communication/images', ['jpeg', 'png', 'jpg'], 10000000);
	
			if(array_key_exists('error', $is_image_file_upload)) {
				$response['errors'] = [
					"images[]" => $is_image_file_upload['message']
				]; 
            	$response['status'] = FALSE;
            	
				return $response;
			}
		}

		if(($_FILES['document']['size']) > 0) {
			$is_doc_file_upload = upload_media('document', 'uploads/communication/documents', ['pdf'], 10000000);
	
			if(array_key_exists('error', $is_doc_file_upload)) {
				$response['errors'] = [
					"document" => $is_doc_file_upload['message']
				]; 
            	$response['status'] = FALSE;
            	
				return $response;
			}
		}
		
		$data = $this->process_data($this->fillable, $_POST);
		$id = $this->_insert($data);

		if(! $id){
            $response['message'] = 'Internal Server Error'; 
            $response['status'] = FALSE;
            return $response;
		}

		if($is_image_file_upload) {
			foreach ($is_image_file_upload as $value) {
				$image_media = [];
				$image_media['c_id'] = $id;
				$image_media['media'] = $value['file_name'];
				$image_media['media_type'] = 'image';
				$image_media['insert_dt'] = $image_media['update_dt'] = date('Y-m-d H:i:s');;
				array_push($media_files, $image_media);
			}
		}

		if($is_doc_file_upload) {
			foreach ($is_doc_file_upload as $value) {
				$document_media = [];
				$document_media['c_id'] = $id;
				$document_media['media'] = $value['file_name'];
				$document_media['media_type'] = 'document';
				$document_media['insert_dt'] = $document_media['update_dt'] = date('Y-m-d H:i:s');;
				array_push($media_files, $document_media);
			}
		}		
		
		if(count($media_files)) {
			$last_media_id = $this->_insert_batch($media_files, 'communication_media');
		}

		$notification_request = [];
		$notification_request['insert_id'] = $id;
		$notification_request['title'] = $data['title'];
		$notification_request['type'] = 'ho_communication';
		$notification_request['desc'] = (!empty($data['description'])) ? $data['description'] : '';

		$req_id = $this->_insert($notification_request, 'notification_request');
		
		$role = isset($_POST['group_id']) ? $_POST['group_id'] : '';

		$selected_roles = isset($_POST['selected_role']) ? array_filter($_POST['selected_roles']) : '';
		//array_filter($_POST['selected_roles'])
		
		if(!empty($selected_roles)){
			foreach($selected_roles as $key => $users){
				$request_devices = [];
				$users;
				$devices_data = $this->get_selected_devices_records($role, $users);
					
				foreach ($devices_data as $device){
	
					if(strlen($device->device_id) < 10) {
						continue;
					}
	
					$requestdevices = [];
					$requestdevices['request_id'] = $req_id;
					$requestdevices['user_id'] = $device->user_id;
					$requestdevices['device_id'] = $device->device_id;
					$requestdevices['device_type'] = $device->device_type;	
					$requestdevices['insert_dt'] = $requestdevices['update_dt'] = date('Y-m-d H:i:s');
					array_push($request_devices, $requestdevices);
				}	
					
			}
		}else{
			$request_devices = [];
			$devices_data = $this->get_devices_records();
					
				foreach ($devices_data as $device){
	
					if(strlen($device->device_id) < 10) {
						continue;
					}
	
					$requestdevices = [];
					$requestdevices['request_id'] = $req_id;
					$requestdevices['user_id'] = $device->user_id;
					$requestdevices['device_id'] = $device->device_id;
					$requestdevices['device_type'] = $device->device_type;	
					$requestdevices['insert_dt'] = $requestdevices['update_dt'] = date('Y-m-d H:i:s');
					array_push($request_devices, $requestdevices);
				}	
		}
				 		
		if(count($request_devices)) {
			$this->_insert_batch($request_devices, 'notification_request_devices');
		}

        $response['status'] = TRUE;
        $response['message'] = 'Congratulations! Details has been added successfully.';
        $response['redirectTo'] = 'communication/lists';

        return $response;
	}
	
	function modify(){
		/*Load the form validation Library*/
		$this->load->library('form_validation');
		$this->load->helper('upload_media');

		$is_Available = $this->check_for_posted_record($this->p_key, $this->table);
		if(! $is_Available['status']){ return $is_Available; }
		
		$this->form_validation->set_rules($this->validate('modify'));

		if(! $this->form_validation->run() ){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');
			print_r($errors);
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}		
		
        $data = $this->process_data($this->fillable, $_POST);

        $p_key = $this->p_key;
		$id = (int) $this->input->post($p_key);
		
		$status = (int) $this->_update([$p_key => $id], $data);
		
		$media_files = [];
		
		if(array_sum($_FILES['images']['size']) > 0) {			
			
			if(count($_FILES['images']) > 5){
				$response['errors'] = [
					"images[]" => 'Maximum 5 Files to be uploaded.'
				]; 
            	$response['status'] = FALSE;
            	
				return $response;
			}

			$is_file_upload = upload_media('images', 'uploads/communication/images', ['jpeg', 'png', 'jpg'], 10000000);
	
			if(array_key_exists('error', $is_file_upload)) {
				$response['errors'] = [
					"images[]" => $is_file_upload['message']
				]; 
            	$response['status'] = FALSE;
            	
				return $response;
			}

			foreach ($is_file_upload as $value) {
				$image_media = [];
				$image_media['c_id'] = $id;
				$image_media['media'] = $value['file_name'];
				$image_media['media_type'] = 'image';
				$image_media['insert_dt'] = $image_media['update_dt'] = date('Y-m-d H:i:s');;
				array_push($media_files, $image_media);
			}
		}

		if(($_FILES['document']['size']) > 0) {
			$is_file_upload = upload_media('document', 'uploads/communication/documents', ['pdf', 'docx', 'doc'], 10000000);
	
			if(array_key_exists('error', $is_file_upload)) {
				$response['errors'] = [
					"document" => $is_file_upload['message']
				]; 
            	$response['status'] = FALSE;
            	
				return $response;
			}

			foreach ($is_file_upload as $value) {
				$document_media = [];
				$document_media['c_id'] = $id;
				$document_media['media'] = $value['file_name'];
				$document_media['media_type'] = 'document';
				$document_media['insert_dt'] = $document_media['update_dt'] = date('Y-m-d H:i:s');;
				array_push($media_files, $document_media);
			}
		}
		
		if(count($media_files)) {
			$last_media_id = $this->_insert_batch($media_files, 'communication_media');
		}
        
        if(! $status){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$response['status'] = TRUE;
        $response['message'] = 'Congratulations! record was updated.';
        
        return $response;
	}

	function download(){

		if(isset($_POST['id'])){
			$doctor_id = (int) $this->input->post('id');
			$insert_user_id = $this->session->get_field_from_session('user_id','user');

			if(!$doctor_id || !$insert_user_id) {
				return;
			}
			
			$response = $this->_insert(
				[
					'doctor_id'=> $doctor_id, 
					'insert_user_id'=> $insert_user_id,
					'share_type'=> 'D'
				], 
				'shared');

			$status = ($response) ? TRUE : FALSE;
			return ['status'=> TRUE];
		}

		return ['msg'=> 'Permission Denied!', 'status'=> FALSE ];
	}

	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['Title'] = $rows['title'];
			$records['Description'] = $rows['description'];
			$records['Date'] = $rows['insert_dt'];

			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}

	function get_selected_devices_records($role, $user_id){
		//echo $role;echo $user_id;
		$role = strtoupper($role);

		$q = $this->db->select('a.user_id, a.device_id, a.device_type')

		->from('access_token a')
		->join('manpower m', 'm.users_id = a.user_id')
		->join('manpower asm', 'asm.users_id = m.users_parent_id')
		->join('manpower zsm', 'zsm.users_id = asm.users_parent_id');

		$q->where('a.token_status', 'active');
		
		if(!empty($role) && $role != 'MR'){
			$alias = ($role == 'ASM') ? 'asm' : 'zsm';
			$q->where($alias.'.users_type', $role);
			if(!empty($user_id)){
				$q->where($alias.'.users_id', $user_id);
			}
			
		}else{
			$q->where('m.users_type', $role);
			if(!empty($user_id)){
				$q->where('m.users_id', $user_id);
			}
			//$q->group_by('m.users_id');
		}

		$q->group_by('a.device_id');
		
		//print_r($this->db->get_compiled_select());exit;
		$collection = $q->get()->result();
		return $collection;
	}

	function get_devices_records(){
		$q = $this->db->select('a.user_id, a.device_id, a.device_type')

		->from('access_token a')
		->join('manpower m', 'm.users_id = a.user_id')
		->where('a.token_status', 'active');

		//print_r($this->db->get_compiled_select());exit;
		$collection = $q->get()->result();
		return $collection;
	}

}
