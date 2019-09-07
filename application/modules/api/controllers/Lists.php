<?php
class Lists extends Api_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('api/mdl_lists', 'model');
		$this->load->library('form_validation');
		
		$this->config->load('s3');
		$this->is_enabled_s3 = $this->config->item('enable_s3');
		$this->s3_base_url = $this->config->item('s3_base_url');
		$this->s3_bucket_name = $this->config->item('s3_bucket_name');

	}

	function chemist_doctor_list()
	{	
		// Get type (doctor, chemist) for app
		// If records are present, return the List on the basis of type to the APP
		// Else return the error message to the APP
		/**
		* @api {post} /api/lists/chemist_doctor_list Chemist Doctor List
		* @apiName chemist_doctor_list
		* @apiGroup Lists
		*
		*@apiParam {String {chemist, doctor}}  cateogry Category.
		*
		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Doctor Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Chemist List",
		*		"error": "",
		*		"code": 200,
		*		data": {
		*    		"list": [
		*        		{
		*            		"d": "1",
		*            		"name": "Ajay",
		*            		"address": "1/6, abc society, India",
		*            		"state": "Gujarat",
		*            		"city": "Bobbili",
		*            		"pincode": "400099",
		*            		"image": []
		*        		},
		*    		],
		*    		"request_id": 1567847910.096092
		*		}
		*	}
		*/

		$user_id = $this->id;

		$category = $this->input_data['category'];

		if($category == 'chemist'){

			$chemistrecords = $this->model->get_records(['users_id' => $user_id], 'chemist');
			$chemistdata = [];

			foreach($chemistrecords as $data){
				$chemist_data['id'] = $data->chemist_id;
				$chemist_data['name'] = $data->chemist_name;
				$chemist_data['address'] = $data->address;
				$state_name = $this->model->get_records(['id' => $data->state] , 'state', ['state']);
				$chemist_data['state'] = !empty($state_name[0]->state) ? $state_name[0]->state : 0;
				$city_name = $this->model->get_records(['city_id' => $data->city] , 'cities', ['city_name']);
				$chemist_data['city'] = !empty($city_name[0]->city_name) ? $city_name[0]->city_name : 0;
				$chemist_data['pincode'] = $data->pincode;
				$chemist_data['image'] = [];
				array_push($chemistdata, $chemist_data);
			}

			$this->response['message'] = empty($chemistrecords) ? "No Chemist Data Found" : "Chemist List";
			$this->response['code'] = 200;
			$this->response['data'] = [
				"list" => $chemistdata,
			];
			$this->sendResponse(); // return the response
		}

		if($category == 'doctor'){

			$doctorrecords = $this->model->get_records(['users_id' => $user_id], 'doctor');
			$doctordata = [];

			foreach($doctorrecords as $value){
				$doctor_data['id'] = $value->doctor_id;
				$doctor_data['name'] = $value->doctor_name;

				$speciality_name = $this->model->get_records(['speciality_id'=>$value->speciality], 'speciality', ['speciality_name']);
				$doctor_data['speciality_name'] = $speciality_name[0]->speciality_name;

				$doctor_data['address'] = $value->address;

				$state_name = $this->model->get_records(['id' => $value->state] , 'state', ['state']);
				$doctor_data['state'] = !empty($state_name[0]->state) ? $state_name[0]->state : 0;

				$city_name = $this->model->get_records(['city_id' => $value->city] , 'cities', ['city_name']);
				$doctor_data['city'] = !empty($city_name[0]->city_name) ? $city_name[0]->city_name : 0;

				$doctor_data['pincode'] = $value->pincode;
				$doctor_data['image'] = [];

				array_push($doctordata, $doctor_data);
			}
		
			$this->response['message'] = empty($doctorrecords) ? "No Doctor Data Found" : "Doctor List";
			$this->response['code'] = 200;
			$this->response['data'] = [
				"list" => $doctordata,
			];
			$this->sendResponse(); // return the response
		}

		
	}

}
