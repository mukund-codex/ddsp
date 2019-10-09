<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_sms extends MY_Model {

	public $p_key = 'id';
	public $table = 'sms_log';

	function __construct() {
		parent::__construct($this->table);
	}

	function get_records_of($field = '', $id_arr = [], $select = [], $table = ''){
		$table = (! empty($table)) ? $table : $this->get_table();

		if(sizeof($select) > 0){
			$this->db->select($select);
		}

		if(sizeof($id_arr) > 0){
			$this->db->where_in($field, $id_arr);
		}

		$this->db->from($table);
		
		$query = $this->db->get();
		return $query->result();
	}

	function get_sms_balance(){
		$this->db->select("ifnull(sum(ceil(length(message)/160)),0) netconsump",false);
		$this->db->from("sms_log"); //define record from which table
		$query = $this->db->get();
		$r = $query->row();
		$netconsump = $r->netconsump;
		
		$this->db->select("ifnull(sum(balance),0 ) netbal",false);
		$this->db->from("sms_balance bal"); //define record from which table
		$query = $this->db->get();
		$r = $query->row();
		$netbal = $r->netbal;
		return $totbal =  $netbal - $netconsump;

	}

	function get_filters() {
        return [
            [],
            [
                'field_name'=>'mobile',
                'field_label'=> 'Mobile',
			],
			[],
			[],
			[]
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

	function get_collection($count = FALSE, $sfilters = [], $rfilters = [], $limit = 0, $offset = 0, ...$params) {
		$field_filters = $this->get_filters_from($rfilters);

    	$q = $this->db->select('
    		sms_log.*
    	')
		->from('sms_log');
				
		if(count($sfilters)) { 
			foreach ($sfilters as $key=>$value) { $q->where("$key", $value); }
		}

		if(is_array($rfilters) && count($rfilters) ) {

            foreach($rfilters as $key=> $value) {
				if(!in_array($key, $field_filters)) {
					continue;
                }
                
				$key = str_replace('|', '.', $key);
                if($key == 'from_date' && $value) {
                    $this->db->where('DATE('.$this->table.'.insertdatetime) >=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if($key == 'to_date' && $value) {
                    $this->db->where('DATE('.$this->table.'.insertdatetime) <=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if(!empty($value))
                    $this->db->like($key, $value);
            }
        }

		if(! $count) {
			$q->order_by('insertdatetime desc');
		}

		if(!empty($limit)) { $q->limit($limit, $offset); }
		// echo $this->db->get_compiled_select(); die();
		$collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		return $collection;
	}	

	function save(){
		/*Load the form validation Library*/
		$this->load->library('form_validation');

		$this->form_validation->set_rules('message','Message','trim|required|xss_clean');
		$this->form_validation->set_rules('sms_type','SMS Type','trim|xss_clean');

		
		$sms_group = isset($_POST['sms_group'])?$_POST['sms_group']:NULL;

		if($sms_group){
			$this->form_validation->set_rules('group_id','Group','required|in_list[HO,ZSM,RSM,ASM,MR,DOCTOR,PATIENT]');
		}

		$selected_role = TRUE;
		if($sms_group == 'group' && $this->input->post('group_id') == 'PATIENT'){
			if(! count(array_filter($this->input->post('selected_roles')))){
				$selected_role = FALSE;
			}
		}

		$selected_patient = TRUE;

		if($sms_group == 'single' && $this->input->post('group_id') == 'PATIENT'){

			$selected_patients = $this->input->post('selected_patients');
			if(empty($selected_patients)) {
				$selected_patient = FALSE;
			}elseif(!count(array_filter($this->input->post('selected_patients')))){
				$selected_patient = FALSE;
			}
		}

		if(!$this->form_validation->run() || !$sms_group || !$selected_role){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');
			
			if(!$sms_group){
				$errors['sms_group'] = '<label class="error">SMS Type Required</label>';
			}

			if(! $selected_role){
				$errors['selected_roles[]'] = '<label class="error">Field is Required</label>';
			}

			if(! $selected_patient){
				$errors['selected_patients[]'] = '<label class="error">Patient selection is Required</label>';
			}

	        $response['errors'] = array_filter($errors); // Some might be empty
	        $response['status'] = FALSE;
		}
		else{

			$data = array();
			$is_success = '';
			
			$message = $this->input->post('message');
			$group_id = $this->input->post('group_id');
			$records = [];

			if($sms_group == 'group'){
				if(in_array($group_id, ['HO', 'ZSM', 'RSM', 'ASM', 'MR'] )){
					$filters = ['users_type'=> $group_id];
					$table = 'manpower';
					$select = ['users_type', 'users_mobile AS mobile'];
				}
				if($group_id == 'DOCTOR'){
					$filters = [];
					$table = 'doctor';
					$select = ['"doctor" AS users_type', 'doc_mobile AS mobile'];
				}
				if($group_id == 'PATIENT'){
					$doctor_id = $this->input->post('selected_roles')[0];
					$filters = ['doctor_id'=> $doctor_id];
					$table = 'patient';
					$select = ['"patient" AS users_type', 'patient_mobile AS mobile'];
				}

				$records = $this->get_records($filters, $table, $select); 
			}
			else{
				if($group_id != 'PATIENT'){
					$id_arr = $this->input->post('selected_roles');
					$id_arr = array_filter($id_arr); 

					if(in_array($group_id, ['HO', 'ZSM', 'RSM', 'ASM', 'MR'] )){
						$records = $this->get_records_of('users_id', $id_arr, ['users_type', 'users_mobile AS mobile'], 'manpower');
					}
					elseif($group_id == 'DOCTOR'){
						$records = $this->get_records_of('doc_id', $id_arr, ['"doctor" AS users_type', 'mobile'], 'doctor');
					}
				}
				else{
					$id_arr = $this->input->post('selected_patients');
					$id_arr = array_filter($id_arr); 

					$records = $this->get_records_of('patient_id', $id_arr, ['"patient" AS users_type', 'mobile_1 AS mobile'], 'patient');
				}
			}

			if(! count($records)){
				return ['status'=> FALSE, 'msg'=> 'No Records for your selection found'];
			}
			
			$sms_collection = [];

			$sms_type = (!empty($this->input->post('sms_type'))) ? $this->input->post('sms_type') : '';

			foreach($records as $record){

				$mobile = $record->mobile;
				$user_type = !empty($sms_type) ? $sms_type : $record->users_type;

				$sms_collection[] = [
					'send_to'=> $mobile, 
					'sms_text'=> $message, 
					'user_group'=> $user_type, 
					'selection_type'=> $sms_group, 
					'insert_dt'=> date('Y-m-d H:i:s')
				];
				//$insertArr['is_valid_request'] = 1;
				//$insertArr['reason'] = $sms_type = $user_type . ' ' . $sms_group .' SMS SENT';

				//send_sms($mobile, $message, $sms_type);
				//$is_success = $this->_insert($insertArr, 'sms_received_log');
			}

			$batch_status = FALSE;
			if(count($sms_collection)){
				$batch_status = (boolean) $this->_insert_batch($sms_collection, 'sms_queue');
				$custom_message = ($batch_status) ? 'Messages are queued for sending will be sent shortly!' : 'Sorry for the inconvenience, please contact technical support!';
			}
			else{
				$custom_message = 'No SMS was queued for sending, please try again if it is really needed!';
			}
			
			$response['status'] = $batch_status;

			if($batch_status){
				$response['message_str'] = $custom_message;
			}else{
				$response['msg'] = $custom_message;
			}
			return $response;
		}
		return $response;
    }
    
    function total_sms() {
        $q = $this->db->select('SUM(balance) AS balance')
            ->from('sms_balance')
            ->get()->result();

        return $q;
    }

	function saveSmsBalance() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('sms_balance','SMS Balance','trim|required|numeric');

		if(!$this->form_validation->run()) {
			$errors = array();
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');

	        $response['errors'] = array_filter($errors); // Some might be empty
	        $response['status'] = FALSE;
		} else {
			$data['balance'] = $this->input->post('sms_balance');
            $balance_id = $this->_insert($data, 'sms_balance');

            // echo $this->db->last_query(); die();
			$response['status'] = ((int) ($balance_id)) ? TRUE : FALSE;
		}
		return $response;
	}
	
	function modify(){
		/*Load the form validation Library*/
		$this->load->library('form_validation');

		$is_Available = $this->check_for_posted_record($this->p_key, $this->table);
		if(! $is_Available['status']){ return $is_Available; }
		
		$sms_name = trim($this->input->post('sms_name'));

		if( strtolower($sms_name) != strtolower($is_Available['data'][0]->sms_name) ){
			$this->form_validation->set_rules('sms_name','Zone Name','trim|required|valid_name|unique_key[sms.sms_name]|max_length[150]|xss_clean');
		}
		else{
			$this->form_validation->set_rules('sms_name','Zone Name','trim|required|valid_name|max_length[150]|xss_clean');
		}

		if(! $this->form_validation->run() ){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');

	        $response['errors'] = array_filter($errors); // Some might be empty
	        $response['status'] = FALSE;
		}		
		else{
			$data = array();
			$data['sms_name'] = $this->input->post('sms_name');

			$p_key = $this->p_key;
			$sms_id = (int) $this->input->post('sms_id');

			$status = (int) $this->_update([$p_key => $sms_id], $data);
			$response['status'] = ($status) ? TRUE : FALSE;
		}
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
			$records['Message For'] = $rows['msg_for'];
			$records['Mobile'] = $rows['mobile'];
			$records['Message'] = $rows['message'];
			$records['Output'] = $rows['output'];
			$records['Date'] = $rows['insertdatetime'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}