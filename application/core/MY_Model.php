<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    private $table;
    private $p_key;
    private $alias;

	function __construct($table = '', $p_key = '', $alias = '') {
		parent::__construct();
		$this->table = $table;
        $this->p_key = $p_key;
        $this->alias = $alias;
	}

	function get_table() {
		return $this->table;
    }

    function get_alias() {
       	return $this->alias;
    }
    
    function get_pkey() {
		return $this->p_key;
	}

	function req_field_error($check_in, $elem, $field_name){
		if($check_in == 'post'){
			return (! isset($_POST[$elem])) ? '<p>'. $field_name .' is required</p>' : '';
		}
		else{
			return ( empty($_FILES[$elem]['name'])) ? '<p>'. $field_name .' is required</p>' : '';
		}
	}

	function file_upload($path, $field_name, $new_name){

		if(!empty($_FILES[$field_name]['name'])){
			$details = upload(array('upload_path'=>$path, 'name'=> $field_name, 'new_name'=> $new_name));

			if(array_key_exists('errors', $details)){
				return ['status'=> FALSE, 'u_response'=> $details];
			}
			elseif(array_key_exists('filename', $details)){
				return ['status'=> TRUE, 'u_response'=> $details];
			}
		}
	}

	function ppt_upload($path, $field_name, $new_name){

		if(!empty($_FILES[$field_name]['name'])){
			$details = upload_ppt(array('upload_path'=>$path, 'name'=> $field_name, 'new_name'=> $new_name));

			if(array_key_exists('errors', $details)){
				return ['status'=> FALSE, 'u_response'=> $details];
			}
			elseif(array_key_exists('filename', $details)){
				return ['status'=> TRUE, 'u_response'=> $details];
			}
		}
	}

	function pdf_upload($path, $field_name, $new_name){

		if(!empty($_FILES[$field_name]['name'])){
			$details = upload_pdf(array('upload_path'=>$path, 'name'=> $field_name, 'new_name'=> $new_name));

			if(array_key_exists('errors', $details)){
				return ['status'=> FALSE, 'u_response'=> $details];
			}
			elseif(array_key_exists('filename', $details)){
				return ['status'=> TRUE, 'u_response'=> $details];
			}
		}
	}

	function video_upload($path, $field_name, $new_name){

		if(!empty($_FILES[$field_name]['name'])){
			$details = upload_video(array('upload_path'=>$path, 'name'=> $field_name, 'new_name'=> $new_name));

			if(array_key_exists('errors', $details)){
				return ['status'=> FALSE, 'u_response'=> $details];
			}
			elseif(array_key_exists('filename', $details)){
				return ['status'=> TRUE, 'u_response'=> $details];
			}
		}
	}

	function _insert($data, $table = '') {
		$table = (! empty($table)) ? $table : $this->get_table();
		$data['insert_dt'] = $data['update_dt'] = date('Y-m-d H:i:s');
		return ($this->db->insert($table, $data)) ? $this->db->insert_id() : FALSE;
	}

	function _insert_batch($data, $table='') {
		$table = (! empty($table)) ? $table : $this->get_table();
		return $this->db->insert_batch($table, $data);
	}

	function _update($conditions = [], $data, $table = '') {
		$table = (! empty($table)) ? $table : $this->get_table();
		$this->db->where($conditions);

		$data['update_dt'] = date('Y-m-d H:i:s');

		return $this->db->update($table, $data);
	}

	function _update_with($field_name, $id_array = [], $conditions = [], $data = [], $table = '') {

		$table = (! empty($table)) ? $table : $this->get_table();

		if(count($conditions)){
			$this->db->where($conditions);
		}

		if(count($id_array)){
			$this->db->where_in($field_name, $id_array);
		}

		$data['update_dt'] = date('Y-m-d H:i:s');

		return $this->db->update($table, $data);
	}

	function _delete($field_name, $id_array = [], $table = '') {

		$table = (! empty($table)) ? $table : $this->get_table();
		$this->db->where_in($field_name, $id_array);
		return $this->db->delete($table);
	}

	function _delete_by_criteria($condition, $table = '') {
		$table = (! empty($table)) ? $table : $this->get_table();
		$this->db->where($condition);
		return $this->db->delete($table);
	}

	function get_records($filters = [], $table = '', $select = [], $order_by = '', $limit = 0, $offset = 0) {
		$table = (! empty($table)) ? $table : $this->get_table();

		if(sizeof($select) > 0){
			$this->db->select($select);
		}

		if(sizeof($filters) > 0){
            foreach($filters as $key => $filter) {
                if(! is_array($filter)) {
                    $this->db->where($key, $filter);
                } 
                
                if (is_array($filter) && count($filter)) {
                    $this->db->where_in($key, $filter);
                }
            }
		}

		if(!empty($order_by)){
			$this->db->order_by($order_by);
		}

		if(!empty($limit)) { $this->db->limit($limit, $offset); }

		$this->db->from($table);
		$query = $this->db->get();
		//echo $this->db->last_query(); die();
		return $query->result();
	}

	function get_or_records($filters = [], $table = '', $select = [], $order_by = '', $limit = 0, $offset = 0) {
		$table = (! empty($table)) ? $table : $this->get_table();

		if(sizeof($select) > 0){
			$this->db->select($select);
		}

		if(sizeof($filters) > 0){
			$this->db->or_where($filters);
		}

		if(!empty($order_by)){
			$this->db->order_by($order_by);
		}

		if(!empty($limit)) { $this->db->limit($limit, $offset); }

		$this->db->from($table);

		$query = $this->db->get();
		return $query->result();
    }
    
	function check_for_posted_record( $field, $table ){
		
		$table = (! empty($table)) ? $table : $this->get_table();
		
        if(! isset($_POST[$field]) ){
			$response['status'] = FALSE;
			$response['error_msg'] = 'Invalid Request.';
			return $response;
		}

		$posted_value = $this->input->post($field);
		$value = $this->db->escape_like_str($posted_value);

		$record = $this->get_records([$field => $value], $table, [], $field, 1);
        // echo $this->db->last_query(); die();
		if(! count($record) ){
			$response['status'] = FALSE;
			$response['error_msg'] = 'Requested record does not exist';
			return $response;
		}

		$response['status'] = TRUE;
		$response['data'] = $record;

		return $response;
	}

	function get_options($s_key = '', $field = '', $filters = [], $offset = 0, $limit = 0, $select = [], $table = ''){

		$table = (! empty($table)) ? $table : $this->get_table();

		$q = (count($select)) ? $this->db->select($select) : $this->db->select("$table.*");
		$q->from($table);
		
		if(sizeof($filters)){
			$q->where($filters);
		}

		if(!empty($s_key)) {

			$where_condition1 = " (" . $field . " like '%". $q->escape_like_str($s_key) ."%') ";
			$q->where($where_condition1, NULL, FALSE);
		}
		// $p_key = $this->p_key;
		$q->order_by("$table.update_dt desc");

		if(!empty($limit)) { $q->limit($limit, $offset); }

		$collection = $q->get()->result();
		return $collection;
    }
    
    function process_data($fillable = [], $data = []) {

        $process_data = [];
        $post_keys = array_intersect($fillable, array_keys($data));

        foreach($post_keys as $key=> $value){
            $process_data[$value] = $this->input->post($value);
        }

        return $process_data;
	}
	
	function user_agent(){
        $this->load->helper('user_agent');
		$data = [];
		
        $data['agent_string'] = user_agent()['agent_string'] ?? NULL;
        $data['platform'] = user_agent()['platform'] ?? NULL;
        $data['browser'] = user_agent()['browser'] ?? NULL;
        $data['version'] = user_agent()['version'] ?? NULL;
        $data['robot'] = (int) user_agent()['robot'] ?? NULL;
        $data['mobile'] = (int) user_agent()['mobile'] ?? NULL;
        $data['referrer'] = (int) user_agent()['referrer'] ?? NULL;
        $data['is_referral'] = (int) user_agent()['is_referral'] ?? NULL;
        $data['languages'] = count(user_agent()['languages']) ? implode(',',user_agent()['languages']) : NULL;
        $data['charsets'] = count(user_agent()['charsets']) ? implode(',',user_agent()['charsets']) : NULL;
        $data['ip_address'] = $this->input->ip_address();
        $data['date'] = date('Y-m-d H:i:s');
        return $data;
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
}
