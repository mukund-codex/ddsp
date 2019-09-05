<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_basic extends MY_Model {

	private $p_key = 'study_id';
	private $table = 'study';
    private $fillable = ['study_name'];

	function __construct() {
		parent::__construct($this->table, $this->p_key);
	}

	function get_collection( $count = FALSE, $sfilters = [], $keywords = '', $limit = 0, $offset = 0, ...$params ) {
        
        $q = $this->db->select('
			study.*
    	')
		->from('study');

		if(sizeof($sfilters)) { 
			foreach ($sfilters as $key=>$value) { $q->where("$key", $value); }
		}

		if(!empty($keywords)) { 
			$s_key = $this->db->escape_like_str($keywords);

			$where_condition = "(
				study_name like '%". $s_key ."%'
			) ";

			$q->where($where_condition, NULL, FALSE);
		}

        if(! $count) {
            $q->order_by('update_dt desc, study_id desc');
        }

		if(!empty($limit)) { $q->limit($limit, $offset); }
        
		$collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		// echo $this->db->last_query(); die();
		return $collection;
	}
	
	function validate($type)
	{	
		if($type == 'save') {
			return [
				[
					'field' => 'study_name',
					'label' => 'Study Name',
					'rules' => 'trim|required|valid_name|unique_key[study.study_name]'
				]
			];
		}

		if($type == 'modify') {
			return [
				[
					'field' => 'study_name',
					'label' => 'Study Name',
					'rules' => 'trim|required|unique_key[study.study_name.study_id.'. (int) $this->input->post('study_id') .']'
				]
			];
		}
    }
    
    function save(){
		/*Load the form validation Library*/
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('app_name','Application Name','trim|required|valid_name|max_length[50]|xss_clean');
		$this->form_validation->set_rules('theme','Theme','trim|required|valid_name|max_length[20]|xss_clean');
		$this->form_validation->set_rules('sender_id','Sender ID','trim|valid_name|max_length[6]|xss_clean');
		
		if(!$this->form_validation->run()){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');
	        
	        $response['errors'] = array_filter($errors); // Some might be empty
	        $response['status'] = FALSE;
		}
		else{
			$data = array();
			$data['app_name'] = $this->input->post('app_name');
			$data['theme'] = $this->input->post('theme');
			$data['sender_id'] = $this->input->post('sender_id');
			$cnt = 0;
			foreach($data as $key=> $value){
				$this->db->where('config_type', $key);
				$updated = $this->db->update('config', ['config_value'=> $value]);

				if($updated){ $cnt++; }
			}

			$response['status'] = (! $cnt) ? FALSE : TRUE;
			$response['redirect'] = 'data';
		}
		return $response;
	}
}
