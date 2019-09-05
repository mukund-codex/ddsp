<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Mdl_language extends MY_Model {

	private $p_key = 'id';
	private $table = 'lang_master';
	private $alias = 'd';
	private $fillable = ['name','code'];
    private $column_list = ['Language Name', 'Language Code', 'Created'];
    private $csv_columns = ['Name','Code','Created'];

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
                'field_name'=>'name',
                'field_label'=> 'Name',
            ],
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
        ->from('lang_master d');
        
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
					'field' => 'name',
					'label' => 'Language Name',
					'rules' => 'trim|required|valid_name|max_length[150]|xss_clean'
                ],
                [
					'field' => 'code',
					'label' => 'Code',
					'rules' => 'trim|required|valid_code|unique_record[add.table.lang_master.code.' . $this->input->post('code') .']|xss_clean'
				]
			];
		}

		if($type == 'modify') {
			return [
				[
					'field' => 'name',
					'label' => 'Language Name',
					'rules' => 'trim|required|valid_name|max_length[150]|xss_clean'
                ],
                [
					'field' => 'code',
					'label' => 'Code',
					'rules' => 'trim|required|valid_mobile|unique_record[edit.table.lang_master.code.' . $this->input->post('code'). '.doctor_id.'. $this->input->post('id') .']|xss_clean'
                ]
			];
		}
    }

	function preview(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->validate('save'));

		$doctor_photo = $this->input->post('imageName');
		
		if(! $this->form_validation->run() || empty($doctor_photo) || !file_exists("uploads/doctors/$doctor_photo")){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
				$errors[$key] = form_error($key, '<label class="error">', '</label>');
				
			if(empty($doctor_photo) || !file_exists("uploads/doctors/$doctor_photo")) {
				$errors['doctor_photo'] = '<label class="error" style="width: fit-content">File Required</label';
			}
	        
	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}

		list($width, $height, $type, $attr) = getimagesize("uploads/doctors/$doctor_photo");
		if($width < 600) {
			$response['errors'] = [
				'doctor_photo' => '<label class="error">Please Upload Good Quality Image</label'
			];
            $response['status'] = FALSE;
            return $response;
		}
	
		$data = $this->process_data($this->fillable, $_POST);
		
		$user_id = $this->session->get_field_from_session('user_id', 'user');
		$data['insert_user_id'] = (int) $user_id;
		
		// Crop Image
		$imageName = $this->input->post('imageName');
		$x1 = (int) $this->input->post('x1');
		$y1 = (int) $this->input->post('y1');
		$x2 = (int) $this->input->post('x2');
		$y2 = (int) $this->input->post('y2');
		
		if($x2 <= 0 || $y2 <= 0) {
			$response['errors'] = [
				'doctor_photo' => '<label class="error">No Image Area Selected</label'
			];
            $response['status'] = FALSE;
            return $response;
		}

        $image_crop = $this->image_crop($imageName, $x1, $y1, $x2, $y2);
		if(!empty($image_crop)) {
			$data['photo'] = $image_crop;
		}
		// END

		$this->load->model('poster/mdl_poster', 'poster');
		$doctor_poster = $this->poster->generate($this->input->post(), $image_crop);

        $response['status'] = TRUE;
		$response['popup'] = TRUE;
		$response['image'] = base_url($doctor_poster);
        return $response;
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
        $response['message'] = 'Congratulations! your Language details is saved with us!.';
        $response['redirectTo'] = 'language/lists';

        return $response;
	}
	
	function image_crop($image, $x_axis, $y_axis, $width, $height, $new_image_name = '')
	{
		$this->load->library('Image');  

		$image_path = "uploads/doctors/$image";
		$file_name = pathinfo($image_path, PATHINFO_FILENAME);
		$new_image_name = ($new_image_name) ? $new_image_name.'-crop.png' : $file_name.'-crop.png';

		if(!file_exists("uploads/doctors/thumbs")) {
			mkdir("uploads/doctors/thumbs", 0755, true);
		}

		$new_file_path = "uploads/doctors/thumbs/$new_image_name";
        $imageObj = new Image("uploads/doctors/$image");
        $imageObj->crop($x_axis, $y_axis, $width, $height);
		$imageObj->save($new_file_path);
		return file_exists($new_file_path) ? $new_file_path : '';
	}
	
	function modify(){
		/*Load the form validation Library*/
		$this->load->library('form_validation');

		/* $is_Available = $this->check_for_posted_record($this->p_key, $this->table);
		if(! $is_Available['status']){ return $is_Available; } */
		
		/* $this->form_validation->set_rules($this->validate('modify')); */

		/* if(! $this->form_validation->run() ){
			$errors = array();	        
	        foreach ($this->input->post() as $key => $value)
	            $errors[$key] = form_error($key, '<label class="error">', '</label>');

	        $response['errors'] = array_filter($errors); // Some might be empty
            $response['status'] = FALSE;
            
            return $response;
		}	 */	
		
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
			$records['Language Name'] = $rows['name'];
			$records['Language Code'] = $rows['code'];
			$records['Created'] = $rows['insert_dt'];

			array_push($resultant_array, $records);
		}
		return $resultant_array;
	}
}
