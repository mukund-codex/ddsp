<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_asm_lists extends MY_Model {

	private $p_key = 'doctor_id';
	private $table = 'doctor';
	private $alias = 'd';
	private $fillable = ['molecule_id','brand_name'];
    private $column_list = ['ABM', 'Area', 'MR Name', 'HQ', 'Doctor Name', 'Speciality', 'Type', 'HyperPigmentation', 'Acne', 'AntiFungal', 'ABM Status', 'ZBM Status', 'Images'];
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

		if($user_role != 'HO' || empty($user_role)){
			array_push($this->column_list,"Action");
		}	

        return $this->column_list;
    }

    function get_filters() {
        $user_role = $this->session->get_field_from_session('role','user');
		$admin_columns = [];
		if(empty($user_role)) {
			$admin_columns = [
				[
					'field_name'=>'temp2|zsm_name',
					'field_label'=> 'ZSM Name',
				],
				[
					'field_name'=>'temp2|zone',
					'field_label'=> 'Zone',
				],
			];
		}
        $user_columns = [
			[
                'field_name'=>'temp2|asm_name',
                'field_label'=> 'ABM Name',
			],
			[
                'field_name'=>'temp2|area',
                'field_label'=> 'Area Name',
			],
			[
                'field_name'=>'temp2|mr_name',
                'field_label'=> 'MR Name',
			],
			[
                'field_name'=>'temp2|city',
                'field_label'=> 'HQ Name',
			],
			[
                'field_name'=>'temp2|doctor_name',
                'field_label'=> 'Doctor Name',
			],
			[
                'field_name'=>'temp2|speciality',
                'field_label'=> 'Doctor Speciality',
			],
			[
                'field_name'=>'temp2|type',
                'field_label'=> 'Doctor Type',
			],
			[],
			[],
			[],
			[
                'field_name'=>'temp2|asm_status',
                'field_label'=> 'ABM Status',
			],
			[
                'field_name'=>'temp2|zsm_status',
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
        
		$sql = "SELECT 
		temp2.* ,
		GROUP_CONCAT(im.image_name) images
		FROM
		(
		SELECT 
		temp.asm_id, temp.zsm_id,
		temp.zsm_name,temp.asm_name, temp.mr_name, 
		temp.zone, temp.area, temp.city,
		temp.doctor_id, temp.doctor_name, temp.speciality, temp.type, temp.asm_status, temp.zsm_status,
		SUM(temp.hyper) hyper, SUM(temp.acne) acne, SUM(temp.anti) anti
		
		FROM (
		SELECT
		mr.users_name as mr_name,
		asm.users_id as asm_id, asm.users_name as asm_name, zsm.users_id as zsm_id, zsm.users_name as zsm_name,
		z.zone_name as zone, a.area_name as area, c.city_name as city,
		d.doctor_id, d.doctor_name, d.asm_status as asm_status, d.zsm_status as zsm_status,
		sp.speciality_name as speciality, spc.category_name as type,
		cat.category_id,cat.category_name,
		b.brand_name as other_name, ub.brand_name,
		IF(cat.category_id = 1,SUM(ub.rxn),0) as hyper,
		IF(cat.category_id = 2,SUM(ub.rxn),0) as acne,
		IF(cat.category_id = 3,SUM(ub.rxn),0) as anti
		FROM
		doctor d
		JOIN speciality sp ON sp.speciality_id = d.speciality
		LEFT JOIN speciality_category spc ON spc.sc_id = d.speciality_category
		JOIN users_molecule um ON um.doctor_id = d.doctor_id
		JOIN category cat ON cat.category_id = um.category_id
		JOIN users_brand ub ON ub.molecule_id = um.molecule_id
		LEFT JOIN brand b ON b.brand_id = ub.brand_id
		JOIN manpower mr ON mr.users_id = d.users_id
		JOIN manpower asm ON asm.users_id = mr.users_parent_id
		JOIN manpower zsm ON zsm.users_id = asm.users_parent_id
		JOIN zone z ON z.zone_id = zsm.users_zone_id
		JOIN area a ON a.area_id = asm.users_area_id
		JOIN city c ON c.city_id = mr.users_city_id
		GROUP BY ub.id
		)temp
		GROUP BY temp.doctor_id
		) temp2
		JOIN images im ON im.doctor_id = temp2.doctor_id AND im.category = 'doctor'";

        $sql .= " WHERE 1 = 1 ";
		
		$role = $this->session->get_field_from_session('role', 'user');
		if(!empty($role)){
			$id = $this->session->get_field_from_session('user_id', 'user');
			if($role == 'ASM'){
				$sql .= "AND temp2.asm_id = '".$id."'";
			}
			if($role == 'ZSM'){
				$sql .= "AND temp2.zsm_id = '".$id."'";
			}
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
                    $sql .= " AND $key LIKE '%$value%' ";
                }
            }
        }

		$sql .= " GROUP BY temp2.doctor_id ";
		$sql .= " ORDER BY temp2.doctor_name ";

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
			$records['Doctor Name'] = $rows['doctor_name'];
			$records['Speciality'] = $rows['speciality'];
			$records['Type'] = $rows['type'];
			$records['HyperPigmentation'] = $rows['hyper'];
			$records['Acne'] = $rows['acne'];
			$records['AntiFungal'] = $rows['anti'];
			$records['ABM Status'] = ucfirst($rows['asm_status']);
			$records['ZBM Status'] = ucfirst($rows['zsm_status']);
			$images = "";
			if(!empty($rows['images'])){
				$rx_files = explode(',', $rows['images']);
				if(count($rx_files)){
					foreach ($rx_files as $key => $value){
						if(file_exists($value)){
							$ext = pathinfo($value, PATHINFO_EXTENSION);
							$images .= base_url($value).", ";
						}
					}
					$records['Images'] = rtrim($images, ', ');
				}
			}
			
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
