<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_mr_lists extends MY_Model {

	private $p_key = 'doctor_id';
	private $table = 'doctor';
	private $alias = 'd';
	private $fillable = ['molecule_id','brand_name'];
	
    private $column_list = ['MR Name', 'HQ', 'Chemist Count', 'Doctor Name', 'Speciality', 'Type', 'HyperPigmentation (Rxn or Strips/Week)', 'Acne (Rxn or Strips/Week)', 'AntiFungal (Rxn or Strips/Week)', 'ABM Status', 'Images', 'Action'];
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
			$admin_column_list = ['ZSM Name', 'Zone', 'ASM Name', 'Area'];
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
					'field_name'=>'temp2|zsm_name',
					'field_label'=> 'ZSM Name',
				],
				[
					'field_name'=>'temp2|zone',
					'field_label'=> 'Zone',
				],
				[
					'field_name'=>'temp2|asm_name',
					'field_label'=> 'ASM Name',
				],
				[
					'field_name'=>'temp2|area',
					'field_label'=> 'Area',
				],
			];
		}
        $user_columns = [			
			[
                'field_name'=>'temp2|mr_name',
                'field_label'=> 'MR Name',
			],
			[
                'field_name'=>'temp2|city',
                'field_label'=> 'HQ Name',
			],
			[],
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
		];
		
		$newcolumns = array_merge($admin_columns, $user_columns);
		$non_filter = [
			[],
		];
		return array_merge($non_filter,$newcolumns);
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
		temp.zone, temp.area, temp.city, temp.chemist_count, temp.chemist_date, 
		temp.doctor_id, temp.doctor_name, temp.speciality, temp.type, temp.asm_status, temp.zsm_status,
		SUM(temp.hyper) hyper, SUM(temp.acne) acne, SUM(temp.anti) anti
		
		FROM (
		SELECT
		mr.users_name as mr_name,
		asm.users_id as asm_id, asm.users_name as asm_name, zsm.users_id as zsm_id, zsm.users_name as zsm_name,
		z.zone_name as zone, a.area_name as area, c.city_name as city, COUNT(ch.chemist_id) as chemist_count,
		ch.insert_dt as chemist_date,
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
		JOIN chemist ch ON ch.chemist_id = d.chemist_id
		GROUP BY ub.id
		)temp
		GROUP BY temp.doctor_id
		) temp2
		LEFT JOIN images im ON im.doctor_id = temp2.doctor_id AND im.category = 'doctor'";

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
			// echo '<pre>';print_r($field_filters);exit;
            foreach($rfilters as $key=> $value) {
                $value = trim($value);
                if(!in_array($key, $field_filters)) {
                    continue;
                }
			   
				if($key == 'from_date' && !empty($value)) {
					$sql .= " AND DATE(temp2.chemist_date) >= '".date('Y-m-d', strtotime($value))."' ";
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
					$sql .= " AND DATE(temp2.chemist_date) <= '".date('Y-m-d', strtotime($value))."' ";
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
		$sql .= " ORDER BY temp2.chemist_date DESC ";

        if(! $count) {
            if(!empty($limit)) { $sql .= " LIMIT $offset, $limit"; }        
        }
        
        $q = $this->db->query($sql);
        //echo $sql;exit;
        $collection = (! $count) ? $q->result_array() : $q->num_rows();

		return $collection;
    }	
	/* function get_collection( $count = FALSE, $sfilters = [], $rfilters = [], $limit = 0, $offset = 0, ...$params ) {
        
        $q = $this->db->select('b.brand_id, b.brand_name, b.insert_dt, b.update_dt, m.molecule_id, m.molecule_name')
		->from('brand b')
		->join('molecule m', 'b.molecule_id = m.molecule_id');
        
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
                    $this->db->where('DATE(b.insert_dt) >=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
                    $this->db->where('DATE(b.insert_dt) <=', date('Y-m-d', strtotime($value)));
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
			$q->order_by('b.brand_id desc');
		}

		if(!empty($limit)) { $q->limit($limit, $offset); }        
        //echo $this->db->get_compiled_select(); die();
        $collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		return $collection;
    } */	
    
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
			print_r($errors);
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}		
		
        $data = $this->process_data($this->fillable, $_POST);

        $p_key = $this->p_key;
        $id = (int) $this->input->post($p_key);

        $status = (int) $this->_update([$p_key => $id], $data);
        $records['HQ City'] = $rows['city'];
			$records['Doctor Name'] = $rows['doctor_name'];
			$records['Speciality'] = $rows['speciality'];
			$records['Type'] = $rows['type'];
			$records['HyperPigmentation'] = $rows['hyper'];
			$records['Acne'] = $rows['acne'];
			$records['AntiFungal'] = $rows['anti'];
			$records['ABM Status'] = ucfirst($rows['asm_status']);
        if(! $status){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$response['status'] = TRUE;
        $response['message'] = 'Congratulations! record was updated.';
        
        return $response;
	}

	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$user_role = $this->session->get_field_from_session('role','user');		
			if(empty($user_role)) {  
				$records['ZSM Name'] = $rows['zsm_name'];
				$records['Zone'] = $rows['zone'];
				$records['ASM Name'] = $rows['asm_name'];
				$records['Area'] = $rows['area'];
			}
			$records['MR Name'] = $rows['mr_name'];
			$records['HQ City'] = $rows['city'];
			$records['Chemist Count'] = $rows['chemist_count'];
			$records['Doctor Name'] = $rows['doctor_name'];
			$records['Speciality'] = $rows['speciality'];
			$records['Type'] = $rows['type'];
			$records['HyperPigmentation'] = $rows['hyper'];
			$records['Acne'] = $rows['acne'];
			$records['AntiFungal'] = $rows['anti'];
			$records['ABM Status'] = ucfirst($rows['asm_status']);
			$records['ZBM Status'] = ucfirst($rows['zsm_status']);		
			$records['Image'] = "";	
			if(!empty($rows['images'])){
				$rx_files = explode(',', $rows['images']);
				if(count($rx_files)){
					foreach ($rx_files as $key => $value){
						if(file_exists($value)){
							$ext = pathinfo($value, PATHINFO_EXTENSION);
							$images = base_url($value);
							$records['Image'] = $images;
						}
						
					}
				}
			}

			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}

	function approve_doctor($doctor_id){

		if(isset($_POST['ids']) && sizeof($_POST['ids']) > 0){
			$ids = $this->input->post('ids');

			$ids = implode("', '",$ids);

		}
		
		$doctor_ids = empty($ids) ? $doctor_id : $ids;

		if(empty($doctor_ids)){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$sql = "UPDATE doctor
		SET asm_status = 'approve'
		WHERE doctor_id IN ('".$doctor_ids."')";

		$q = $this->db->query($sql);

		$response['status'] = TRUE;
		$response['message'] = 'Congratulations! Doctor Approved successfully.';
		$response['redirectTo'] = 'mr_lists/lists';

		return $response;

	}

	function disapprove_doctor($doctor_id){

		if(isset($_POST['ids']) && sizeof($_POST['ids']) > 0){

			$ids = $this->input->post('ids');

			$ids = implode("', '",$ids);

		}
		
		$doctor_ids = empty($ids) ? $doctor_id : $ids;
		
		if(empty($doctor_ids)){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$sql = "UPDATE doctor
		SET asm_status = 'disapprove'
		WHERE doctor_id IN ('".$doctor_ids."')";

		$q = $this->db->query($sql);

		$response['status'] = TRUE;
		$response['message'] = 'Congratulations! Doctor Disapproved successfully.';
		$response['redirectTo'] = 'mr_lists/lists';

		return $response;

	}

	function getBrandMolecules($doctor_id, $category_id) {
		$q = $this->db->select('
			m.molecule_name, b.brand_name,ub.brand_id, 
			ub.brand_name as custom_brand_name, ub.rxn')
        ->from('users_molecule um')
        ->join('users_brand ub', 'ub.molecule_id = um.molecule_id')
		->join('molecule m', 'm.molecule_id = um.molecule')
		->join('brand b', 'b.brand_id = ub.brand_id', 'left');

		$q->where('ub.doctor_id', $doctor_id);
		$q->where('um.category_id', $category_id);

		//echo $this->db->get_compiled_select(); die();
		$collection = $q->get()->result_array();
		return $collection;
	}
}