<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_rsm extends Manpower_Model {

	private $p_key = 'users_id';
    private $table = 'manpower';
    private $tb_alias = 'm';
    private $fillable = ['users_name', 'users_mobile', 'users_emp_id', 'users_password', 'users_zone_id', 'users_region_id'];
	private $column_list = ['RSM Name', 'Mobile', 'Emp ID', 'Password', 'Region Name', 'ZSM Name', 'Zone Name','Created On'];
    private $csv_columns = ['RSM Name', 'Mobile', 'Emp ID', 'Password', 'Region Name', 'Zone Name'];

	function __construct() {
        parent::__construct();
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
                'field_name'=>'m|users_name',
                'field_label'=> 'RSM',
            ], 
            [
                'field_name'=>'m|users_mobile',
                'field_label'=> 'Mobile',
            ], 
            [
                'field_name'=>'m|users_emp_id',
                'field_label'=> 'Emp ID',
            ], 
            [],
            [
                'field_name'=>'region_name',
                'field_label'=> 'Region',
            ], 
            [
                'field_name'=>'us.users_name',
                'field_label'=> 'ZSM',
            ], 
            [
                'field_name'=>'zone_name',
                'field_label'=> 'Zone',
            ], 
        ];
    }

    function get_filters_from(array $filters): array {
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
     	$q = $this->db->select('
            m.users_id, m.users_name, m.users_mobile, m.users_emp_id, m.users_parent_id,
            m.users_type, m.users_password, us.users_name as mgr_name,
            z.zone_id, z.zone_name, r.region_id, r.region_name,
            m.insert_dt
    	')
		->from('manpower m')
        ->join('region r', 'm.users_region_id = r.region_id')
        ->join('zone z', 'r.zone_id = z.zone_id')
		->join('manpower us', 'm.users_parent_id = us.users_id')
        ->where('m.users_type', 'RSM');
				
		if(sizeof($sfilters)) { 
			foreach ($sfilters as $key=>$value) { $q->where("$key", $value); }
		}


		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                if(!in_array($key, $field_filters)) {
                    continue;
                }
                
                $key = str_replace('|', '.', $key);

                if($key == 'from_date' && $value) {
                    $this->db->where('DATE('.$this->tb_alias.'.insert_dt) >=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if($key == 'to_date' && $value) {
                    $this->db->where('DATE('.$this->tb_alias.'.insert_dt) <=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if(!empty($value))
                    $this->db->like($key, $value);
            }
        }

		if(! $count) {
			$q->order_by('m.update_dt desc');
		}

		if(!empty($limit)) { $q->limit($limit, $offset); }     
		$collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		return $collection;
    }	
    
    function validate($type)
	{
        $role = 'RSM';

		if($type == 'save') {

			return [
                [
					'field' => 'users_zone_id',
					'label' => 'Zone',
					'rules' => 'trim|required|check_record[zone.zone_id]|xss_clean'
                ],
                [
					'field' => 'users_region_id',
					'label' => 'Region',
					'rules' => 'trim|required|check_record[region.region_id]|unique_record[add.table.manpower.users_type.'. $role .'.users_region_id.' . $this->input->post('users_region_id') . ']|xss_clean'
                ],
				[
					'field' => 'users_name',
					'label' => 'Name',
					'rules' => 'trim|required|valid_name|max_length[50]|xss_clean'
                ],
				[
					'field' => 'users_mobile',
					'label' => 'Mobile',
					'rules' => 'trim|max_length[10]|valid_mobile|unique_key[manpower.users_mobile]|xss_clean'
                ],
				[
					'field' => 'users_emp_id',
					'label' => 'Emp ID',
					'rules' => 'trim|required|alpha_numeric|max_length[10]|unique_key[manpower.users_emp_id]|xss_clean'
                ],
				[
					'field' => 'users_password',
					'label' => 'Password',
					'rules' => 'trim|required|max_length[50]|xss_clean'
                ]
			];
		}

		if($type == 'modify') {
            
            return [
                [
					'field' => 'users_zone_id',
					'label' => 'Zone',
					'rules' => 'trim|required|check_record[zone.zone_id]|xss_clean'
                ],
                [
					'field' => 'users_region_id',
					'label' => 'Region',
					'rules' => 'trim|required|check_record[region.region_id]|unique_record[edit.table.manpower.users_type.'. $role .'.users_region_id.' . (int) $this->input->post('users_region_id') . '.users_id.'. $this->input->post('users_id') .']|xss_clean'
                ],
				[
					'field' => 'users_name',
					'label' => 'Name',
					'rules' => 'trim|required|valid_name|max_length[50]|xss_clean'
                ],
				[
					'field' => 'users_mobile',
					'label' => 'Mobile',
					'rules' => 'trim|max_length[10]|valid_mobile|unique_key[manpower.users_mobile.users_id.'. (int) $this->input->post('users_id') .']|xss_clean'
                ],
				[
					'field' => 'users_emp_id',
					'label' => 'Emp ID',
					'rules' => 'trim|required|alpha_numeric|max_length[10]|unique_key[manpower.users_emp_id.users_id.'. (int) $this->input->post('users_id') .']|xss_clean'
                ],
				[
					'field' => 'users_password',
					'label' => 'Password',
					'rules' => 'trim|required|max_length[50]|xss_clean'
                ]
			];
		}
    }

	function save(){
		/*Load the form validation Library*/
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
		
		$users_parent_info = $this->user_info($_POST['users_zone_id'], 'ZSM');
		if(! $users_parent_info) {
			$response['errors'] = ["users_name" => "<label class='error'>Manager Does not Exist</label>"];
			$response['status'] = FALSE;
			return $response;
		}

		$data = $this->process_data($this->fillable, $_POST);
		$data['users_parent_id'] = $users_parent_info['users_id'];
        $data['users_type'] = 'RSM';
		
        $id = $this->_insert($data);
        
        if(! $id){
            $response['message'] = 'Internal Server Error';
            $response['status'] = FALSE;
            return $response;
        }

        $response['status'] = TRUE;
        $response['message'] = 'Congratulations! new record created.';

        return $response;
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
		
		$users_parent_info = $this->user_info($_POST['users_zone_id'], 'ZSM');
		if(! $users_parent_info) {
			$response['errors'] = ["users_name" => "<label class='error'>Manager Does not Exist</label>"];
			$response['status'] = FALSE;
			return $response;
		}

		$data['users_parent_id'] = $users_parent_info['users_id'];
        if(empty($data['users_mobile'])) {
            $data['users_mobile'] = NULL;
        }

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

	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['RSM Name'] = $rows['users_name'];
			$records['Mobile'] = $rows['users_mobile'];
			$records['EMP ID'] = $rows['users_emp_id'];
			$records['Region'] = $rows['region_name'];
			$records['ZSM Name'] = $rows['mgr_name'];
			$records['Zone'] = $rows['zone_name'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}