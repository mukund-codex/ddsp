<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_city extends MY_Model {

	private $p_key = 'city_id';
    private $table = 'city';
    private $fillable  = ['city_name', 'area_id'];
    private $column_list =  ['City Name', 'Area Name', 'Region Name', 'Zone Name', 'Created On'];
    private $csv_columns = ['Zone Name', 'Region Name', 'Area Name', 'City Name'];

	function __construct() {
        parent::__construct($this->table, $this->p_key);
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
                'field_name'=>'city_name',
                'field_label'=> 'City',
            ], 
            [
                'field_name'=>'area_name',
                'field_label'=> 'Area',
            ], 
            [
                'field_name'=>'region_name',
                'field_label'=> 'Region',
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
            zone.zone_id, zone.zone_name, 
            region.region_id, region.region_name,
            area.area_id, area.area_name,
            city.city_id, city.city_name, city.insert_dt
    	')
        ->from('city')
        ->join('area', 'area.area_id = city.area_id')
        ->join('region', 'region.region_id = area.region_id')
        ->join('zone', 'region.zone_id = zone.zone_id');
				
		if(sizeof($sfilters)) { 
			foreach ($sfilters as $key=>$value) { $q->where("$key", $value); }
		}


		if(is_array($rfilters) && count($rfilters) ) {
			$field_filters = $this->get_filters_from($rfilters);
			
            foreach($rfilters as $key=> $value) {
                if(!in_array($key, $field_filters)) {
                    continue;
                }
                
                if($key == 'from_date' && !empty($value)) {
                    $this->db->where('DATE('.$this->table.'.insert_dt) >=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if($key == 'to_date' && !empty($value)) {
                    $this->db->where('DATE('.$this->table.'.insert_dt) <=', date('Y-m-d', strtotime($value)));
                    continue;
                }

                if(!empty($value))
                    $this->db->like($key, $value);
            }
        }

		if(! $count) {
			$q->order_by('city.update_dt desc, city_id desc');
		}

		if(!empty($limit)) { $q->limit($limit, $offset); }        
		$collection = (! $count) ? $q->get()->result_array() : $q->count_all_results();
		return $collection;
    }	
    
    function validate($type)
	{
		if($type == 'save') {
			return [
                [
					'field' => 'zone_id',
					'label' => 'Zone Name',
					'rules' => 'trim|required|check_record[zone.zone_id]|xss_clean'
                ],
                [
					'field' => 'region_id',
					'label' => 'Region Name',
					'rules' => 'trim|required|check_record[region.region_id]|xss_clean'
                ],
                [
					'field' => 'area_id',
					'label' => 'Area Name',
					'rules' => 'trim|required|check_record[area.area_id]|xss_clean'
                ],
				[
					'field' => 'city_name',
					'label' => 'City Name',
					'rules' => 'trim|required|valid_name|max_length[150]|unique_record[add.table.city.city_name.' . $this->input->post('city_name') . '.area_id.'. (int) $this->input->post('area_id') .']|xss_clean'
                ]
			];
		}

		if($type == 'modify') {
			return [
                [
					'field' => 'zone_id',
					'label' => 'Zone Name',
					'rules' => 'trim|required|check_record[zone.zone_id]|xss_clean'
                ],
                [
					'field' => 'region_id',
					'label' => 'Region Name',
					'rules' => 'trim|required|check_record[region.region_id]|xss_clean'
                ],
                [
					'field' => 'area_id',
					'label' => 'Area Name',
					'rules' => 'trim|required|check_record[area.area_id]|xss_clean'
                ],
				[
					'field' => 'city_name',
					'label' => 'City Name',
					'rules' => 'trim|required|valid_name|max_length[150]|unique_record[edit.table.city.city_name.' . $this->input->post('city_name') . '.area_id.'. (int) $this->input->post('area_id') .'.city_id.'. (int) $this->input->post('city_id') .']|xss_clean'
				],
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
			$records['City Name'] = $rows['city_name'];
			$records['Area Name'] = $rows['area_name'];
			$records['Region Name'] = $rows['region_name'];
			$records['Zone Name'] = $rows['zone_name'];
			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}