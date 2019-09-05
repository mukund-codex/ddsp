<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_ho extends MY_Model {

	private $p_key = 'users_id';
    private $table = 'manpower';
    private $tb_alias = 'm';
	private $fillable = ['users_name', 'users_mobile', 'users_emp_id', 'users_password'];
	private $column_list = ['HO Name', 'HO Mobile', 'HO Employee Id', 'HO Password', 'Created On'];
    private $csv_columns = ['HO Name','HO Mobile', 'HO Employee Id', 'HO Password'];

	function __construct() {
        parent::__construct($this->table, $this->p_key, $this->tb_alias);
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
				'field_name'=>'users_name',
				'field_label'=> 'HO Name',
			],
			[
				'field_name'=>'users_mobile',
				'field_label'=> 'HO Mobile',
			],
			[
				'field_name'=>'users_emp_id',
				'field_label'=> 'Employee Id',
			],
			[
				'field_name'=>'users_password',
				'field_label'=> 'HO Password',
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

	function get_collection($count = FALSE, $f_filters = [], $rfilters = [], $limit = 0, $offset = 0 ) {

		$field_filters = $this->get_filters_from($rfilters);
    	$q = $this->db->select('
            m.users_id, m.users_name, m.users_mobile, m.users_emp_id, 
            m.users_type, m.users_password, 
            m.insert_dt
    	')
        ->from('manpower m')
        ->where('m.users_type', 'HO');
				
		if(sizeof($f_filters)) { 
			foreach ($f_filters as $key=>$value) { $q->where("$key", $value); }
		}

		if(is_array($rfilters) && count($rfilters) ) {

            foreach($rfilters as $key=> $value) {
                if(!in_array($key, $field_filters)) {
                    continue;
                }
                
                if($key == 'from_date' && $value) {
                    $this->db->where('DATE('.$this->table.'.insert_dt) >=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if($key == 'to_date' && $value) {
                    $this->db->where('DATE('.$this->table.'.insert_dt) <=', date('Y-m-d', strtotime($value)));
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
        $role = 'HO';

		if($type == 'save') {
			return [
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
		
        $data = $this->process_data($this->fillable, $_POST);
        $data['users_type'] = 'HO';

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

	function remove(){
		
		if(isset($_POST['ids']) && sizeof($_POST['ids']) > 0){
			$ids = $this->input->post('ids');
			$response = $this->_delete($this->p_key, $ids, $this->table);

			$msg = ($response) ? "Record(s) Successfully deleted" : 'Error while deleting record(s)';
			return ['msg'=> $msg];
		}

		return ['msg'=> 'No Records Selected'];
	}

	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['HO Name'] = $rows['users_name'];
			$records['Mobile'] = $rows['users_mobile'];
			$records['EMP ID'] = $rows['users_emp_id'];
			$records['Password'] = $rows['users_password'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}