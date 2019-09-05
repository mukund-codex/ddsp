<?php
class Doctor extends Api_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('api/mdl_doctor', 'model');
		$this->load->library('form_validation');
		
		$this->config->load('s3');
		$this->is_enabled_s3 = $this->config->item('enable_s3');
		$this->s3_base_url = $this->config->item('s3_base_url');
		$this->s3_bucket_name = $this->config->item('s3_bucket_name');

	}

	function doctor_list()
	{
		// Doctor List To the System
		/**
		* @api {post} /api/doctor/doctor_list Doctor List
		* @apiName doctor_list
		* @apiGroup Doctor
		*
		*
		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Doctor Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Doctor List",
		*		"error": "",
		*		"data": {
		*			"doctor": [
		*				{
		*					"doctor_id": "1",
		*					"doctor_name": "Jimish",
		*					"doctor_email": "jimish@techizer",
		*					"clinic_address": "test",
		*					"photo": "http://localhost/zydus-doctor-poster/uploads/doctorImages/fef34ce114a2f14392b637d819831683.jpg",
		*					"generated_template": "http://localhost/zydus-doctor-poster/uploads/posters/test-doctor-86955.jpg",
		*					"download_url": "http://192.168.0.3/cadila-ipledge/share/index/20/0",
		*					"share_url": "http://localhost/zydus-doctor-poster/uploads/posters/test-doctor-86955.jpg"
		*				}
		*			],
		*			"request_id": 1562226550.03435802459716796875
		*		},
		*		"code": 200
		*	}
		*/

		$user_id = $this->id;
		$userRecords = $this->model->get_records(['users_id' => $user_id], 'manpower');
		$user_type = $userRecords[0]->users_type;
		$get_doctor_list = $this->model->get_collection($user_id, $user_type);
		$data = [];
		if(count($get_doctor_list) > 0)  {
			foreach ($get_doctor_list as $key => $value) {
				$input_data['doctor_id'] 			= $value['doctor_id'];
				$input_data['doctor_name'] 			= $value['doctor_name'];
				$input_data['doctor_email'] 		= $value['doctor_email'];
				$input_data['clinic_address'] 		= $value['clinic_address'];
				$input_data['speciality_name'] 		= $value['speciality_name'];
				$input_data['speciality_id'] 		= $value['speciality_id'];
				$input_data['photo'] 				= ($value['photo']) ? base_url().$value['photo']: "";
				$input_data['generated_template'] 	= $value['poster'] ? $this->s3_base_url.$value['s3_url']: "";
				$input_data['download_url'] 		= base_url()."api/share/index/".$value['doctor_id']."/0";
				$input_data['share_url'] 			= base_url()."api/share/index/".$value['doctor_id']."/1";
				array_push($data, $input_data);
			}
		}

		$this->response['data'] = [
			"doctor" => $data
		];

		$this->response['message'] = empty($data) ? "No Doctor Found" : "Doctor List";
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
	}

	public function add_doctor() {
		// Add Doctor To the System
		/**
		* @api {post} /api/doctor/add_doctor Add Doctor
		* @apiName add_doctor
		* @apiGroup Doctor
		*

		*
		* @apiParam {String} doctor_name Doctor name
		* @apiParam {String} doctor_id Doctor ID(In Edit Mode)
		* @apiParam {Number} doctor_email Email Id
		* @apiParam {Number} type Type of Operation
		* @apiParam {Number} clinic_address Doctor Clinic Address(Max 150 characters)
		* @apiParam {File} photo Doctor Photo(JPEG, JPG, PNG)

		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Doctor Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Doctor Added Successfully!",
		*		"error": "",
		*		"code": 200,
		*		"data": {
		*			"request_id": 1561630916.4044220447540283203125
		*		}
		*	}
		*/
		
		$doctor_name = $this->input->post('doctor_name');
		$doctor_id = $this->input->post('doctor_id');
		$doctor_email = $this->input->post('doctor_email');
		$speciality_id = $this->input->post('speciality_id');
		$type = $this->input->post('type');
		// $doctor_mobile = $this->input->post('doctor_mobile');
		$clinic_address = $this->input->post('clinic_address');
		$photo = $this->input->post('photo');
		$user_id = $this->id;
		
		$type = in_array($type, ['add', 'edit']) ? $type : 'add';

		if(!$doctor_name) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor Name is Required!";
			$this->error = array('message'=>'Doctor Name is Mandatory!');
			$this->sendResponse();
			return;
		}

		$doctorData = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor');
		if(empty($doctorData) && $type == 'edit') {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor ID is required for Edit!";
			$this->error = array('message'=>'Doctor ID is required for Edit!');
			$this->sendResponse();
			return;
		}

		$doctorsCount = $this->model->get_records(['doctor_mr_id' => $this->id], 'doctor');
		/* Remove This Feature now (Updated By Jimish On ON 23-8-19) as client dont want this feature now */
		/* if(count($doctorsCount) >= 12) {
			$this->response['code'] = 400;
			$this->response['message'] = "No Doctors can be added!";
			$this->error = array('message'=>'No Doctors can be added!');
			$this->sendResponse();
			return;
		} */

		$specialityData = $this->model->get_records(['speciality_id' => $speciality_id], 'speciality');
		if(empty($specialityData)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Speciality ID is required!";
			$this->error = array('message'=>'Speciality ID is required!');
			$this->sendResponse();
			return;
		}

		if(!$doctor_email) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor Email Id is Required!";
			$this->error = array('message'=>'Doctor Email Id is Mandatory!');
			$this->sendResponse();
			return;
		}

		$check_email = true;
		if($type == 'edit') {
			if($doctorData[0]->doctor_email == $doctor_email) {
				$check_email = false;
			}
		}

		if($check_email) {
			$doctorExistingRecords = $this->model->get_records(['doctor_email' => $doctor_email], 'doctor');
			if(!empty($doctorExistingRecords)) {
				$this->response['code'] = 400;
				$this->response['message'] = "Doctor Email Already exists!";
				$this->error = array('message'=>'Doctor Email already exists!');
				$this->sendResponse();
				return;
			}
		}

		if(!$this->form_validation->valid_email($doctor_email)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Email Id!";
			$this->error = array('message'=>'Doctor Email Invalid!');
			$this->sendResponse();
			return;
		}

		if(!preg_match('/^[a-z0-9\040\.\-\']+$/i', $doctor_name) || strlen($doctor_name) > 25 ) {
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Name";
			$this->error = array('message'=>'Doctor Name Invalid!');
			$this->sendResponse();
			return;
		}

		/* Check for Valid File */
		if(empty($_FILES) && $type == 'add') {
			$this->response['code'] = 400;
			$this->response['message'] = "Photo is Mandatory!";
			$this->error = array('message'=>'Photo Mandatory!');
			$this->sendResponse();
			return;
		}

		if(!$clinic_address) {
			$this->response['code'] = 400;
			$this->response['message'] = "Clinic Address is Required!";
			$this->error = array('message'=>'Clinic Address is Mandatory!');
			$this->sendResponse();
			return;
		}

		if(strlen($clinic_address) > 80) {
			$this->response['code'] = 400;
			$this->response['message'] = "Clinic Address too long!";
			$this->error = array('message'=>'Clinic Address too long!');
			$this->sendResponse();
			return;
		}

		if(!empty($_FILES['photo'])) {
			
			$this->load->helper('upload_media');
			$is_file_upload = upload_media('photo', 'uploads/doctorImages/', ['jpeg', 'png', 'jpg'], 10000000);
	
			if(array_key_exists('error', $is_file_upload)) {
				$this->response['code'] = 400;
				$this->response['message'] = $is_file_upload['error'];
				$this->error = array('message'=> $is_file_upload['error']);
				$this->sendResponse();
				return;
			}
		}

		if(substr(strtolower($doctor_name),0,3) == 'dr.' || substr(strtolower($doctor_name),0,3) == 'dr ')  {
			// Do Nothing
		} else {
			$doctor_name = "Dr. ".trim($doctor_name);
		}

		/* Insert Doctor Data */
		
		$doctorData = ['doctor_name' 		=> ucwords($doctor_name),
						'doctor_email'		=> $doctor_email,
						'speciality_id'		=> $speciality_id,
						'doctor_mr_id'		=> $user_id,
						'clinic_address'	=> $clinic_address
					];

		if(!empty($is_file_upload)) {
			$doctorData['photo'] = $is_file_upload[0]['file_name'];
		}

		if($type == 'add') {
			$doctor_id = $this->model->_insert($doctorData, 'doctor');
		} else {
			$this->model->_update(['doctor_id' => $doctor_id], $doctorData, 'doctor');
		}

		$data['doctor_id'] = $doctor_id;
		$this->response['message'] = empty($doctorData) ? "Internal Server Error" : "Doctor Added Successfully!";
		$this->response['data'] = $data;
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
	}

	public function doctor_upload_media()
	{

		// Add Doctor To the System
		/**
		* @api {post} /api/doctor/doctor_upload_media Add Doctor Photo
		* @apiName doctor_upload_media
		* @apiGroup Doctor
		*
		*
		* @apiParam {String} doctor_id Doctor Id
		* @apiParam {File} photo Doctor Photo

		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Doctor Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Photo Uploaded Successfully!",
		*		"error": "",
		*		"code": 200,
		*		"data": {
		*			"request_id": 1561630964.6844971179962158203125
		*		}
		*	}
		*/

		$this->load->helper('upload_media');
		$doctor_id = trim($this->input->post('doctor_id') ?  $this->input->post('doctor_id') : '');
		$doctorData = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor');

		if(empty($doctorData)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor is Mandatory";
			$this->error = array('message'=>'Doctor Mandatory!');
			$this->sendResponse();
			return;
		}

		if(empty($_FILES)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Photo is Mandatory!";
			$this->error = array('message'=>'Photo Mandatory!');
			$this->sendResponse();
			return;
		}

		$is_file_upload = upload_media('photo', 'uploads/doctorImages/', ['jpeg', 'png', 'jpg'], 10000000);

		if(array_key_exists('error', $is_file_upload)) {
			$this->response['code'] = 400;
			$this->response['message'] = $is_file_upload['error'];
			$this->error = array('message'=> $is_file_upload['error']);
			$this->sendResponse();
			return;
		}

		$image_name = $is_file_upload[0]['file_name'];
		$updateData = array('photo'	=> $image_name);
		$condition = array('doctor_id' => $doctor_id);
		$this->model->_update($condition, $updateData, 'doctor');

		$this->response['message'] = "Photo Uploaded Successfully!";
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
	}

	function share_template() {

		// Share Template To the System
		/**
		* @api {post} /api/doctor/share_template Share Template
		* @apiName share_template
		* @apiGroup Doctor
		*
		*
		* @apiParam {String} doctor_id Doctor Id

		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Doctor Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Shared Successfully!",
		*		"error": "",
		*		"code": 200,
		*		"data": {
		*			"request_id": 1561630964.6844971179962158203125
		*		}
		*	}
		*/
		
		$doctor_id = trim($this->input_data['doctor_id'] ?  $this->input_data['doctor_id'] : '');
		$doctorData = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor');

		if(empty($doctorData)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor is Mandatory";
			$this->error = array('message'=>'Doctor Mandatory!');
			$this->sendResponse();
			return;
		}

		$doctorPoster = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor_poster');
		if(empty($doctorPoster)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor Poster not generated";
			$this->error = array('message'=>'Doctor Poster not generated');
			$this->sendResponse();
			return;
		}

		$file_path = $doctorPoster[0]->s3_url;
		$insertData = ['doctor_id' => $doctor_id, 'type' => "share", 'file_path' => $file_path];
		$this->model->_insert($insertData, 'image_status');

		$this->response['message'] = "Shared Successfully!";
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
	}
}