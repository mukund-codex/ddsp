<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_password extends MY_Model {

	private $p_key = 'id';
	private $table = 'manpower';
	private $alias = 'm';
	private $fillable = ['password', 're_password'];
    private $column_list = ['Doctor Name', 'Question 1', 'Question 2', 'Question 3', 'Created'];
    private $csv_columns = ['Doctor Name', 'Question 1', 'Question 2', 'Question 3', 'Created'];

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
                'field_name'=>'code',
                'field_label'=> 'Code',
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
        
        $q = $this->db->select('d.*')
        ->from('bunch d');
        
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
                    $this->db->where('DATE('.$this->alias.'.insert_dt) >=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
                    $this->db->where('DATE('.$this->alias.'.insert_dt) <=', date('Y-m-d', strtotime($value)));
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
			$q->where('d.insert_user_id', $this->session->get_field_from_session('user_id', 'user'));
		}

		if(! $count) {
			$q->order_by('d.update_dt desc');
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
					'field' => 'question',
					'label' => 'Question',
					'rules' => 'trim|required|in_list[question1,question2,question3]|xss_clean'
				]
			];
		}

    }

	function save(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->validate('save'));
		$this->form_validation->set_message('in_list', 'Invalid {field} value.');
		if(! $this->form_validation->run()){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
				$errors[$key] = form_error($key, '<label class="error" style="color:red;">', '</label>');
				
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}
		$question = $this->input->post('question');
		$data['doctor_id'] = (int) $this->input->post('doctor_id');
		$data[$question] = $this->input->post('answer');

		if($question == 'question3'){ 
			if(strtoupper($data[$question]) === 'Y') {
				if(empty($this->input->post('email_id'))) {
					$response['errors'] = ['email_id' => '<label class="error" style="color:red;">This field is required.</label>'];
					$response['status'] = FALSE;
					return $response;
				} 

				if(!filter_var($this->input->post('email_id'), FILTER_VALIDATE_EMAIL) || strlen($this->input->post('email_id')) > 100) {
					$response['errors'] = ['email_id' => '<label class="error" style="color:red;">Invalid Email Format.</label>'];
					$response['status'] = FALSE;
					return $response;
				}

			}
			
		}
		
		$feedback_record = $this->model->get_records(['doctor_id'=> $data['doctor_id'], 'complete_status' => 0], 'feedback', [], '', 1);
		$doctor_id = $feedback_record[0]->doctor_id;
		
		if(empty($doctor_id)){
			$id = $this->_insert($data, 'feedback');
		}else{
			if($question == 'question3'){ 
				$data['complete_status'] = 1; 
				$data['email_id'] = $this->input->post('email_id'); 
			}
			
			$id = $this->_update(['doctor_id' => $doctor_id,'complete_status' => 0], $data, 'feedback');
		}		
	
        if(! $id){
            $response['message'] = 'Internal Server Error';
            $response['status'] = FALSE;
            return $response;
        }

		$response['status'] = TRUE;
		if($question == 'question1'){
			$response['redirectTo'] = 'question2?id='.$data['doctor_id'];
		}else if($question == 'question2'){
			$response['redirectTo'] = 'question3?id='.$data['doctor_id'];
		}else if($question == 'question3'){
			$response['redirectTo'] = 'feedback/thank_you';
		}
        
        return $response;
	}
	
	function _format_data_to_export($data){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {
			$records['Bunch Code'] = $rows['code'];
			$records['Created'] = $rows['insert_dt'];

			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}
