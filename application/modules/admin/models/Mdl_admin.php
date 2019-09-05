<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_admin extends MY_Model {

	private $p_key = 'user_id';
	private $table = 'admin';
    private $fillable = ['full_name', 'username', 'password', 'user_type', 'email', 'mobile'];
    private $column_list = ['Name', 'Username', 'Type', 'Email', 'Mobile', 'Created On'];
    private $csv_columns = ['Username', 'Type', 'Full Name'];

	function __construct() {
		parent::__construct($this->table, $this->p_key);
		$this->session_key = config_item('session_data_key');

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
                'field_name'=>'full_name',
                'field_label'=> 'Name',
            ], 
            [
                'field_name'=>'username',
                'field_label'=> 'Username',
            ], 
            [
                'field_name'=>'user_type',
                'field_label'=> 'Type',
            ], 
            [
                'field_name'=>'email',
                'field_label'=> 'Email',
            ], 
            [
                'field_name'=>'mobile',
                'field_label'=> 'Mobile',
            ], 
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
        $field_filters = $this->get_filters_from($rfilters);

        $q = $this->db->select('
            admin.user_id, admin.full_name, 
            admin.username, admin.password, admin.user_type as admin_role_id, 
			admin.email, admin.mobile, admin.insert_dt,
			roles.role as user_type, roles.label as actual_user_type
    	')
		->from('admin')
		->join('roles', 'admin.user_type = roles.role_id', 'LEFT');

		if(sizeof($sfilters)) { 
			foreach ($sfilters as $key=>$value) { $q->where("$key", $value); }
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
            $q->order_by('admin.update_dt desc, user_id desc');
        }

		if(!empty($limit)) { $q->limit($limit, $offset); }
        
		$collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		return $collection;
	}	

	function get_last_registration_from($from_date){
		$curr_date = date('Y-m-d'); 
		$query = "select d.date AS registration_date, count(v.user_id) AS user_count from 
		(select adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) date from
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
		 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) d
		left join users v on DATE(d.date) = DATE(v.insert_dt)
		where DATE(d.date) between '$from_date' and '$curr_date'
		group by DATE(d.date)
		order by DATE(d.date)";

		$collection = $this->db->query($query)->result_array();
		return $collection;
	}

	function save(){
		$this->load->library(['form_validation', 'encryption']);

		if ( $this->form_validation->run('admin_save') == FALSE){
			
			$errors = array();	        
			foreach ($this->input->post() as $key => $value)
				$errors[$key] = form_error($key, '<label class="error">', '</label>');
	        
	        $response['errors'] = array_filter($errors); // Some might be empty
			$response['status'] = FALSE;
			
			return $response;
		}
		$post_keys = array_intersect($this->fillable, array_keys($_POST));

		foreach($post_keys as $key=> $value){
			
			if(empty($_POST[$value]) && in_array($value, ['email', 'mobile']) ){
				continue;
			}
			$data[$value] = $this->input->post($value);
        }
        
        $data['password'] = Encryption::encryptPassword($data['password']);

		$id = $this->_insert($data);

		if(! $id){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$response['status'] = TRUE;
		$response['message'] = 'Congratulations! you have successfully created a new admin.';

		return $response;
	}

	function modify(){
		/*Load the form validation Library*/
		$this->load->library(['form_validation','encryption']);

		$is_Available = $this->check_for_posted_record($this->p_key, $this->table);
		if(! $is_Available['status']){ return $is_Available; }
		
		if(! $this->form_validation->run() ){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');

	        $response['errors'] = array_filter($errors); // Some might be empty
			$response['status'] = FALSE;
			
			return $response;
		}		
		
		$post_keys = array_intersect($this->fillable, array_keys($_POST));

		foreach($post_keys as $key=> $value){
			
			if(empty($_POST[$value]) && in_array($value, ['email', 'mobile']) ){
				$data[$value] = NULL;
			}else{
				$data[$value] = $this->input->post($value);
			}
		}

		$user_id = (int) $this->input->post('user_id');

		if($data['password']) {
			$oldHash = $is_Available['data'][0]->password;
			$data['password'] = Encryption::encryptPassword($data['password']);
		}

		$status = $this->_update([$this->p_key => $user_id], $data);

		if(! $status){
			$response['message'] = 'Internal Server Error';
			$response['status'] = FALSE;
			return $response;
		}

		$response['status'] = TRUE;
		$response['message'] = 'Congratulations! you have successfully updated the record.';

		return $response;
    }

	function _format_data_to_export($data){
		
		$resultant_array = [];

		foreach ($data as $rows) {
			$records['Name'] = $rows['full_name'];
			$records['User Name'] = $rows['username'];
			$records['User Type'] = $rows['actual_user_type'];
			$records['Email'] = $rows['email'];
			$records['Mobile'] = $rows['mobile'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}
