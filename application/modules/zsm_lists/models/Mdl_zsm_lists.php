<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_zsm_lists extends MY_Model {

	private $p_key = 'doctor_id';
	private $table = 'doctor';
	private $alias = 'd';
	private $fillable = ['molecule_id','brand_name'];
    private $column_list = ['ABM', 'Area', 'MR Name', 'HQ', 'Chemist Name', 'Chemist Address', 'Doctor Name', 'Doctor Address', 'ABM Status', 'ZBM Status', 'Action'];
    private $csv_columns = ['ABM', 'Area', 'MR Name', 'HQ', 'Chemist Name', 'Doctor Name'];

	function __construct() {
        parent::__construct($this->table, $this->p_key,$this->alias);
    }
    
    function get_csv_columns() {
        return $this->csv_columns;
    }

    function get_column_list() {
		$user_role = $this->session->get_field_from_session('role','user');
		
		$columns = [];
		if(empty($user_role)) {            //For Admin Login
			$admin_column_list = ['ZSM Name', 'Zone'];
            $this->column_list = array_merge($admin_column_list, $this->column_list);
		}

        return $this->column_list;
    }

    function get_filters() {
        $user_role = $this->session->get_field_from_session('role','user');
		$admin_columns = [];
		if(empty($user_role)) {
			$admin_columns = [
				[
					'field_name'=>'zsm|users_name',
					'field_label'=> 'ZSM Name',
				],
				[
					'field_name'=>'z|zone_name',
					'field_label'=> 'Zone',
				],
			];
		}
        $user_columns = [
			[
                'field_name'=>'asm|users_name',
                'field_label'=> 'ABM Name',
			],
			[
                'field_name'=>'a|area_name',
                'field_label'=> 'Area Name',
			],
			[
                'field_name'=>'mr|users_name',
                'field_label'=> 'MR Name',
			],
			[
                'field_name'=>'c|city_name',
                'field_label'=> 'HQ Name',
			],
			[
                'field_name'=>'ch|chemist_name',
                'field_label'=> 'Chemist Name',
			],
			[
                'field_name'=>'ch|address',
                'field_label'=> 'Chemist Address',
			],
			[
                'field_name'=>'dr|doctor_name',
                'field_label'=> 'Doctor Name',
			],
			[
                'field_name'=>'dr|address',
                'field_label'=> 'Doctor Address',
			],
			[
                'field_name'=>'dr|asm_status',
                'field_label'=> 'ABM Status',
			],
			[
                'field_name'=>'dr|zsm_status',
                'field_label'=> 'ZBM Status',
			],
		];
		
		return array_merge($admin_columns, $user_columns);
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
	function get_collection($count = FALSE, $f_filters = [], $rfilters ='', $limit = 0, $offset = 0 ) {
        
        $sql = "select 

		zsm.users_name as zsm_name,
		z.zone_name as zone,
		asm.users_name as asm_name,
		a.area_name as area,
		mr.users_name as mr_name,
		c.city_name as city,
		ch.chemist_name as chemist_name,
		ch.address as chemist_address,
		dr.doctor_name as doctor_name,
		dr.address as doctor_address,
		dr.doctor_id, dr.asm_status, dr.zsm_status
			
		from manpower mr
		JOIN manpower asm ON mr.users_parent_id = asm.users_id 
		JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
		JOIN zone z ON z.zone_id = zsm.users_zone_id 
		JOIN area a ON a.area_id = asm.users_area_id
		JOIN city c ON c.city_id = mr.users_city_id
		JOIN chemist ch ON ch.users_id = mr.users_id
		JOIN doctor dr ON dr.chemist_id = ch.chemist_id";

        $sql .= " WHERE 1 = 1 ";
		
		$role = $this->session->get_field_from_session('role', 'user');
		if(!empty($role)){
			$id = $this->session->get_field_from_session('user_id', 'user');
		
			$sql .= "AND zsm.users_id = '".$id."'";
		}
		

		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }
               
                if(!empty($value)) {
                    $key = str_replace('|', '.', $key);
                    $value = $this->db->escape_like_str($value);
                    $sql .= " AND $key LIKE '$value%' ";
                }
            }
        }

        //$sql .= " group by asm.users_id";

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        //echo $sql;
        $collection = (! $count) ? $q->result_array() : $q->num_rows();

		return $collection;
    }		
    
    function validate($type)
	{
		if($type == 'save') {
			return [
				[
					'field' => 'molecule_id',
					'label' => 'Molecule Name',
					'rules' => 'trim|required|xss_clean'
				],
                [
					'field' => 'brand_name',
					'label' => 'brand Name',
					'rules' => 'trim|required|max_length[150]|unique_record[add.table.brand.brand_name.' . $this->input->post('brand_name') .']|xss_clean'
                ],
				
			];
		}

		if($type == 'modify') {
			return [
				[
					'field' => 'molecule_id',
					'label' => 'Molecule Name',
					'rules' => 'trim|required|xss_clean'
				],
				[
					'field' => 'brand_name',
					'label' => 'brand Name',
					'rules' => 'trim|required|max_length[150]|unique_record[edit.table.brand.brand_name.' . $this->input->post('brand_name'). '.brand_id.'. $this->input->post('brand_id') .']|xss_clean'
                ],
                
			];
		}
    }

	function save(){
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules($this->validate('save'));
		
		if(! $this->form_validation->run()){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
				$errors[$key] = form_error($key, '<label class="error">', '</label>');
				
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}
		
		$data = $this->process_data($this->fillable, $_POST);
		
/* 
		$user_id = $this->session->get_field_from_session('user_id');
		$data['insert_user_id'] = (int) $user_id; */
		
		$id = $this->_insert($data);
		
        if(! $id){
            $response['message'] = 'Internal Server Error'; 
            $response['status'] = FALSE;
            return $response;
		}

		/* $to = $data['mobile'];
		$msg = $tiny_url;
		$msg_for = "Invitation"; */

		//$this->sendsms($to, $msg, $msg_for);

        $response['status'] = TRUE;
        $response['message'] = 'Congratulations! brand has been added successfully.';
        $response['redirectTo'] = 'brand/lists';

        return $response;
	}

	function get_tiny_url($url){

		$this->load->helper('tiny_url');

		$tiny_url = tiny_url($url);
		
		if(empty($tiny_url)){
			$tiny_url = $this->get_tiny_url($url);
		}

		return $tiny_url;

	}

    function random_strings($length_of_string) 
    { 
    
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
    
		$key = substr(str_shuffle($str_result), 0, $length_of_string); 

		$key_record = $this->model->get_records(['key'=> $key], 'doctor', ['key'], '', 1);
		if(count($key_record)) {
			$key = $this->random_strings($length_of_string);
		}else{
			return $key;
		}
    } 
	
	function modify(){
		/*Load the form validation Library*/
		$this->load->library('form_validation');

		$is_Available = $this->check_for_posted_record($this->p_key, $this->table);
		if(! $is_Available['status']){ return $is_Available; }
		
		$this->form_validation->set_rules($this->validate('modify'));

		if(! $this->form_validation->run() ){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');
			
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}		
		
        $data = $this->process_data($this->fillable, $_POST);

        $p_key = $this->p_key;
        $id = (int) $this->input->post($p_key);

        $status = (int) $this->_update([$p_key => $id], $data);
        
        if(! $status){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$response['status'] = TRUE;
        $response['message'] = 'Congratulations! record was updated.';
        
        return $response;
	}

	function sendsms($to, $msg, $msg_for){

		$this->load->helper('send_sms');

		send_sms($to, $msg, $msg_for);
		//$this->helper->send_sms();

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

	function whatsapp(){

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
					'share_type'=> 'W'
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
			$user_role = $this->session->get_field_from_session('role','user');		
			if(empty($user_role)) {  
				$records['ZBM Name'] = $rows['zsm_name'];
				$records['Zone'] = $rows['zone'];
			}
			
			$records['ABM Name'] = $rows['asm_name'];
			$records['Area'] = $rows['area'];
			$records['MR Name'] = $rows['mr_name'];
			$records['HQ City'] = $rows['city'];
			$records['Chemist Name'] = $rows['chemist_name'];
			$records['Doctor Name'] = $rows['doctor_name'];
			$records['ABM Status'] = ucfirst($rows['asm_status']);
			$records['ZBM Status'] = ucfirst($rows['zsm_status']);

			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}

	function approve_doctor($doctor_id){

		if(empty($doctor_id)){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$records = $this->get_records(['doctor_id' => $doctor_id], 'doctor', [] , '' ,1);
		if(empty($records)){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$data['zsm_status'] = 'approve';

		$id = $this->_update(['doctor_id' => $doctor_id], $data, 'doctor');

		if($id){
			$response['status'] = TRUE;
			$response['message'] = 'Congratulations! Doctor Approved successfully.';
			$response['redirectTo'] = 'asm_lists/lists';

			return $response;
		}

	}

	function disapprove_doctor($doctor_id){

		if(empty($doctor_id)){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$records = $this->get_records(['doctor_id' => $doctor_id], 'doctor', [] , '' ,1);
		if(empty($records)){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$data['zsm_status'] = 'disapprove';

		$id = $this->_update(['doctor_id' => $doctor_id], $data, 'doctor');

		if($id){
			$response['status'] = TRUE;
			$response['message'] = 'Congratulations! Doctor Disapproved successfully.';
			$response['redirectTo'] = 'asm_lists/lists';

			return $response;
		}

	}

}
