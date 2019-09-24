<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_state extends MY_Model {

	private $p_key = 'id';
	private $table = 'state';
	private $alias = 's';
	private $fillable = ['state'];
    private $column_list = ['State Name', 'Created'];
    private $csv_columns = ['State Name'];

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
                'field_name'=>'state',
                'field_label'=> 'State',
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
        
        $q = $this->db->select('s.*')
        ->from('state s');
        
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

		if(! $count) {
			$q->order_by('s.update_dt desc');
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
					'field' => 'state',
					'label' => 'State',
					'rules' => 'trim|required|unique_record[add.table.state.state.' . $this->input->post('state') .']|xss_clean'
				]
			];
		}

		if($type == 'modify') {
			return [
                [
					'field' => 'state',
					'label' => 'State',
					'rules' => 'trim|required|unique_record[edit.table.state.state.' . $this->input->post('state'). '.id.'. $this->input->post('id') .']|xss_clean'
                ]
			];
		}
    }

	function save(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->validate('save'));
	
		$data = $this->process_data($this->fillable, $_POST);
		
		$id = $this->_insert($data);
        
        if(! $id){
            $response['message'] = 'Internal Server Error';
            $response['status'] = FALSE;
            return $response;
        }

        $response['status'] = TRUE;
        $response['message'] = 'Congratulations! your State is saved with us!.';
        $response['redirectTo'] = 'state/lists';

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
			$records['State'] = $rows['state'];
			$records['Created'] = $rows['insert_dt'];

			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}
