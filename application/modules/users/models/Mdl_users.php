<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_users extends MY_Model {

	public $p_key = 'users_id';
	public $table = 'manpower';

	function __construct() {
		parent::__construct($this->table);
		$this->type_array = ['zsm', 'asm','mr'];	
	}

	private function get_user_data($id){
		$q = "SELECT users_id, users_name, users_mobile, users_type, users_parent_id FROM manpower WHERE users_id = $id";
		$data = $this->db->query($q)->result_array();

		if(count($data)){
			$parent_id = $data[0];
		}else{
			$parent_id = [];
		}

		return $parent_id;
	}

	function get_heirarchy_through_city($city){
		
		$city_data = $this->get_records(['users_city_id'=> $city, 'users_type'=> 'MR'], 'manpower', [], '',1);
		$parents = [];
		if(count($city_data)){
			
			$manpower['users_id'] = $city_data[0]->users_id;
			$manpower['users_name'] = $city_data[0]->users_name;
			$manpower['users_mobile'] = $city_data[0]->users_mobile;
			$manpower['users_type'] = $city_data[0]->users_type;
			$manpower['users_parent_id'] = $parent_id = $city_data[0]->users_parent_id;

			array_push($parents, $manpower);

			while($parent_id){
				$parent_data = $this->get_user_data($parent_id);
				
				if(count($parent_data)){
					array_push($parents, $parent_data);
					$parent_id = $parent_data['users_parent_id'];
				}else{
					$parent_id = 0;
				}
			}
		}
		
		return $parents;
	}

	function get_collection($filters = [], $keywords ='', $limit = 0, $offset = 0 ) {

    	$q = $this->db->select('
			u.*, us.users_name as mgr_name, 
			zone_name, region_name, 
			area_name,city_name
		')
		->from('manpower u')
		->join('manpower us', 'u.users_parent_id = us.users_id', 'LEFT')
		->join('zone z', 'u.users_zone_id = z.zone_id', 'LEFT')
		->join('region r', 'u.users_region_id = r.region_id', 'LEFT')
		->join('area a', 'u.users_area_id = a.area_id', 'LEFT')
		->join('city c', 'u.users_city_id = c.city_id', 'LEFT');
		
		if(sizeof($filters)){
			foreach ($filters as $key => $value) {
				$q->where('u.'.$key, $value);
			}
		}

		if(!empty($keywords)) { 
			$sk_words = $this->db->escape_like_str($keywords);

			$where_condition1 = "(
				u.users_name like '%". $sk_words ."%' 
				OR u.users_mobile like '". $sk_words ."%' 
				OR u.users_emp_id like '". $sk_words ."%' 
				OR zone_name like '". $sk_words ."%' 
				OR region_name like '". $sk_words ."%' 
				OR area_name like '". $sk_words ."%' 
				OR city_name like '". $sk_words ."%' 
				OR us.users_name like '%". $sk_words ."%'
			) ";

			$q->where($where_condition1, NULL, FALSE);

			$q->order_by('u.update_dt desc, u.users_id desc');
		}
		else{
			$q->order_by('u.update_dt desc, u.users_id desc');
		}

		if(!empty($limit)) { $q->limit($limit, $offset); }
		
		$collection = $q->get()->result();
		// echo $this->db->last_query(); die();
		return $collection;
	}

	function get_user_info($filters = []){
		
		$q = $this->db->select('u.*, zone_name, region_name, area_name')
		->from('manpower u')
		->join('zone z', 'u.users_zone_id = z.zone_id', 'LEFT')
		->join('region r', 'u.users_region_id = r.region_id', 'LEFT')
		->join('area a', 'u.users_area_id = a.area_id', 'LEFT')
		->join('city c', 'u.users_city_id = c.city_id', 'LEFT');
		
		if(sizeof($filters)){
			$q->where($filters);
		}	

		$query = $q->get();
		return $query->result();
	}

	function get_user_options($s_key = '', $field = '', $filters = [], $offset = 0, $limit = 0, $select = [], $table = ''){
		
		$q = $this->db->select("
			u.users_id, u.users_name, mgr.users_name AS mgr_name, 
			u.users_parent_id, u.users_type,
			u.users_zone_id, u.users_region_id, 
			u.users_area_id,u.users_city_id,
			zone_name, region_name, city_name,
			area_name
		")
		->from('manpower u')
		->join('zone', 'u.users_zone_id = zone_id', 'LEFT')
		->join('region', 'u.users_region_id = region_id', 'LEFT')
		->join('area', 'u.users_area_id = area_id', 'LEFT')
		->join('city', 'u.users_city_id = city_id', 'LEFT')
		->join('manpower mgr', 'u.users_parent_id = mgr.users_id', 'LEFT');
		
		if(sizeof($filters)){
			foreach ($filters as $key => $value) {
				$q->where("u.$key", $value);
			}
			//$q->where($filters);
		}

		if(!empty($s_key)) { 
			
			$where_condition1 = " (u." . $field . " like '%". $q->escape_like_str($s_key) ."%') ";
			$q->where($where_condition1, NULL, FALSE);
		}
		$p_key = $this->p_key;
		$q->order_by("u.$p_key desc");

		if(!empty($limit)) { $q->limit($limit, $offset); }
		$collection = $q->get()->result();
		return $collection;
	}

	function is_a_unique_user( $type = NULL, $cols = [], $geo_id = FALSE, $new_geo_id = FALSE ){
		
		if( empty($type) || ! count($cols) ){
			return TRUE;
		}

		if($geo_id && $new_geo_id && ($geo_id == $new_geo_id)){
			return TRUE;
		}

		$filters = [ 'users_type'=> $type ];

		if( count($cols) ){
			foreach ($cols as $key => $value) {
				$filters[$key] = $value;
			}
		}

		$r_records = $this->get_records($filters);
		return ( count($r_records) ) ? FALSE : TRUE;
	}	

	/*$data is userId and $geo is used for ensuring geography selected is correct*/
	function check($data, $table){
		if( empty($data) ){
			return TRUE;
		}

		$fieldName = ($table == 'manpower') ? 'users_id' :  $table . '_id';
		$records = $this->get_records([$fieldName => $data], $table);
		
		return (count($records)) ? TRUE : FALSE; 
	}

	function save(){
		$u_type = $this->input->post('u_type');
		
		if(! in_array($u_type, $this->type_array) ){
			$response['status'] = FALSE;
			return $response;
		}

		/*loading form validation library*/
		$this->load->library('form_validation');

		$this->form_validation->set_rules('users_name', strtoupper($u_type) .' Name','trim|required|valid_name|max_length[100]|xss_clean');

		$this->form_validation->set_rules('users_mobile', strtoupper($u_type) .' Mobile','trim|required|valid_mobile|unique_key[manpower.users_mobile]|xss_clean');

		if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
			$this->form_validation->set_rules('parent_id', 'Selection','trim|required|is_natural_no_zero|callback_check[manpower]|xss_clean');
		}

		$users_type = $u_type;
		$cols = [];

		if($u_type == 'ho'){
			$this->form_validation->set_rules('users_password','Password', 'trim|required|xss_clean');
		}
		
		if($u_type == 'asm'){
			// $this->form_validation->set_rules('users_password','Password', 'trim|required|xss_clean');
		}


		/*validation that works only for zsm*/
		if($u_type == 'zsm' || $u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
			$this->form_validation->set_rules('users_zone_id','Zone','trim|required|is_natural_no_zero|callback_check[zone]|xss_clean');

			$cols['users_zone_id'] = (int) $this->input->post('users_zone_id');	
			$error_label = 'zone';
		}

		/*validation that works only for rsm*/
		if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
			$this->form_validation->set_rules('users_region_id','Region','trim|required|is_natural_no_zero|callback_check[region]|xss_clean');

			$cols['users_region_id'] = (int) $this->input->post('users_region_id');	
			$error_label = 'region';
		}

		/*validation that works only for asm*/
		if($u_type == 'asm' || $u_type == 'mr'){
			$this->form_validation->set_rules('users_area_id','Area','trim|required|is_natural_no_zero|callback_check[area]|xss_clean');

			$cols['users_area_id'] = (int) $this->input->post('users_area_id');		
			$error_label = 'area';
		}

		/* validation that works only for mr */
		if($u_type == 'mr'){
			$this->form_validation->set_rules('users_city_id','City','trim|required|is_natural_no_zero|callback_check[city]|xss_clean');

			$cols['users_city_id'] = (int) $this->input->post('users_city_id');		
			$error_label = 'city';
		}

		$valid_user = $this->is_a_unique_user($users_type, $cols);
		
		$this->form_validation->set_message('check', '%s does not exists');
		$this->form_validation->set_message('is_natural_no_zero', 'Invalid %s');


		if(! $this->form_validation->run($this) || ! $valid_user ){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');
	        
	        if(! $valid_user){
	        	$errors['users_name'] = '<label class="error">' . strtoupper($u_type) . ' already exists for the selected ' . $error_label . '</label>';
	        }

	        $response['errors'] = array_filter($errors); // Some might be empty
	        $response['status'] = FALSE;
		}
		else{
			$data = array();
			
			$data['users_name'] = $this->input->post('users_name');
			$data['users_mobile'] = $this->input->post('users_mobile');

			if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
				$data['users_parent_id'] = (int) $this->input->post('parent_id');
			}

			if($u_type == 'ho'){
				$data['users_password'] = $this->input->post('users_password');
			}
			
			if($u_type == 'zsm' || $u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
				$data['users_zone_id'] = (int) $this->input->post('users_zone_id');
			}

			if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
				$data['users_region_id'] = (int) $this->input->post('users_region_id');
			}

			if($u_type == 'asm' || $u_type == 'mr'){
				$data['users_area_id'] = (int) $this->input->post('users_area_id');
				// $data['users_password'] = $this->input->post('users_password');
			}

			if($u_type == 'mr'){
				$data['users_city_id'] = (int) $this->input->post('users_city_id');
				// $data['users_password'] = $this->input->post('users_password');
			}

			$data['users_type'] = strtoupper($u_type);

			if($this->_insert($data)):
				$response['status'] = TRUE;
				$response['redirect'] = 'lists/all/' . $u_type;
			else:
				$response['status'] = FALSE;
			endif;
		}
		return $response;
	}
	
	function modify(){
		$u_type = $this->input->post('u_type');
		/*check for user type*/
		if(! in_array($u_type, $this->type_array)){
			$response['status'] = FALSE;
			return $response;
		}

		/*check for user_id format : should be positive natural number*/
		if(!isset($_POST['users_id']) || !preg_match('/^[1-9][0-9]{0,15}$/', $_POST['users_id'])){
			$response['status'] = FALSE;
			return $response;
		}

		$users_id = intval($_POST['users_id']);

		/*loading form validation library*/
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('users_name', strtoupper($u_type) .' Name','trim|required|valid_name|max_length[100]|xss_clean');

		/*getting users current record and data to check for update*/
		$user_record = $this->get_records(['users_id'=> $users_id], 'manpower');
		
		/*fields having users current value which is requested for update*/
		$mobileNo = $user_record[0]->users_mobile;
		$emp_id = $user_record[0]->users_emp_id;
		$users_zone_id = (int) $user_record[0]->users_zone_id;
		$users_region_id = (int) $user_record[0]->users_region_id;
		$users_area_id = (int) $user_record[0]->users_area_id;
		$users_city_id = (int) $user_record[0]->users_city_id;
		/*====================END================*/

		$new_mobile = $this->input->post('users_mobile');
		if($mobileNo != $new_mobile){
			$this->form_validation->set_rules('users_mobile', strtoupper($u_type) .' Mobile','trim|required|valid_mobile|unique_key[manpower.users_mobile]|xss_clean');	
		}else{
			$this->form_validation->set_rules('users_mobile', strtoupper($u_type) .' Mobile','trim|required|valid_mobile|xss_clean');
		}
		/*check ends*/

		if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
			$this->form_validation->set_rules('parent_id', 'Selection','trim|required|is_natural_no_zero|callback_check[manpower]|xss_clean');
		}

		if($u_type == 'ho'){
			$this->form_validation->set_rules('users_password','Password', 'trim|required|xss_clean');
		}

		if($u_type == 'asm' || $u_type == 'mr'){
			// $this->form_validation->set_rules('users_password','Password', 'trim|required|xss_clean');

			//$this->form_validation->set_rules('division_id','Division','trim|required|is_natural_no_zero|callback_check[division]|xss_clean');
		}

		$users_type = $u_type;
		$cols = [];

		/*validation that works only for zsm*/
		if($u_type == 'zsm' || $u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
			
			if( $users_zone_id != (int) $this->input->post('users_zone_id') ){

				$this->form_validation->set_rules('users_zone_id','Zone','trim|required|is_natural_no_zero|callback_check[zone]|xss_clean');

				$cols['users_zone_id'] = (int) $this->input->post('users_zone_id');	
				$error_label = 'zone';	
			}
		}

		/*validation that works only for rsm*/
		if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){

			if( $users_region_id != (int) $this->input->post('users_region_id') ){

				$this->form_validation->set_rules('users_region_id','Region','trim|required|is_natural_no_zero|callback_check[region]|xss_clean');

				$cols['users_region_id'] = (int) $this->input->post('users_region_id');	
				$error_label = 'region';
			}
		}

		/*validation that works only for asm*/
		if($u_type == 'asm' || $u_type == 'mr'){

			if( $users_area_id != (int) $this->input->post('users_area_id') ){

				$this->form_validation->set_rules('users_area_id','Area','trim|required|is_natural_no_zero|callback_check[area]|xss_clean');

				$cols['users_area_id'] = (int) $this->input->post('users_area_id');		
				$error_label = 'area';
			}
		}

		/*validation that works only for mr*/
		if($u_type == 'mr'){

			if( $users_city_id != (int) $this->input->post('users_city_id') ){

				$this->form_validation->set_rules('users_city_id','Area','trim|required|is_natural_no_zero|callback_check[area]|xss_clean');

				$cols['users_city_id'] = (int) $this->input->post('users_city_id');		
				$error_label = 'city';
			}
		}

		switch(strtoupper($u_type))	{
			case 'ZSM':
				$geo_id = $users_zone_id;
				$new_geo_id = (int) $this->input->post('users_zone_id');
				break;
			case 'RSM':
				$geo_id = $users_region_id;
				$new_geo_id = (int) $this->input->post('users_region_id');
				break;
			case 'ASM':
				$geo_id = $users_area_id;
				$new_geo_id = (int) $this->input->post('users_area_id');
				break;
			case 'MR':
				$geo_id = $users_city_id;
				$new_geo_id = (int) $this->input->post('users_city_id');
				break;
			default:
				$geo_id = '';
				$new_geo_id = '';
				break;
		} 

		$valid_user = $this->is_a_unique_user($users_type, $cols, $geo_id, $new_geo_id);

		$this->form_validation->set_message('check', '%s does not exists');
		$this->form_validation->set_message('is_natural_no_zero', 'Invalid %s');

		if(!$this->form_validation->run($this) || ! $valid_user ){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');
	        
	        if(! $valid_user){
	        	$errors['users_name'] = '<label class="error">' . strtoupper($u_type) . ' already exists for the selected ' . $error_label . '</label>';
	        }

	        $response['errors'] = array_filter($errors); // Some might be empty
	        $response['status'] = FALSE;
		}		
		else{
			$data = array();

			$data['users_name'] = $this->input->post('users_name');
			$data['users_mobile'] = $this->input->post('users_mobile');

			if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
				$data['users_parent_id'] = (int) $this->input->post('parent_id');
			}

			if($u_type == 'ho'){
				$data['users_password'] = $this->input->post('users_password');
			}

			if($u_type == 'zsm' || $u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
				$data['users_zone_id'] = (int) $this->input->post('users_zone_id');
			}

			if($u_type == 'rsm' || $u_type == 'asm' || $u_type == 'mr'){
				$data['users_region_id'] = (int) $this->input->post('users_region_id');
			}

			if($u_type == 'asm' || $u_type == 'mr'){
				$data['users_area_id'] = (int) $this->input->post('users_area_id');
				// $data['users_password'] = $this->input->post('users_password');
			}

			if($u_type == 'mr'){
				$data['users_city_id'] = (int) $this->input->post('users_city_id');
				// $data['users_password'] = $this->input->post('users_password');
			}

			$data['users_type'] = strtoupper($u_type);
		
			$p_key = $this->p_key;
			if($this->_update([$p_key => $users_id], $data)):
				$response['status'] = TRUE;
				$response['redirect'] = 'lists/all/' . $u_type;
			else:
				$response['status'] = FALSE;
			endif;
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

	function _format_data_to_export($data, $user_type){
		
		$resultant_array = [];
		
		foreach ($data as $rows) {

			$records[strtoupper($user_type).' Name'] = $rows->users_name;
			$records[strtoupper($user_type).' Mobile'] = $rows->users_mobile;
			
			switch ($user_type) {
				case 'ho':
					$records['Password'] = $rows->users_password;
					break;

				case 'zsm':
					$records['Zone Name'] = $rows->zone_name;
					break;
				
				case 'rsm':
					$records['Region Name'] = $rows->region_name;
					$records['ZSM Name'] = $rows->mgr_name;
					$records['Zone Name'] = $rows->zone_name;
					break;

				case 'asm':
					$records['Area Name'] = $rows->area_name;
					$records['RSM Name'] = $rows->mgr_name;
					$records['Region Name'] = $rows->region_name;
					break;

				case 'mr':
					$records['City Name'] = $rows->city_name;
					$records['ASM Name'] = $rows->mgr_name;
					$records['Area Name'] = $rows->area_name;
					break;

				default:
					# code...
					break;
			}
			
			array_push($resultant_array, $records);

		}
		return $resultant_array;
	}
}