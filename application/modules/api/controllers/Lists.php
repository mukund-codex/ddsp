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
		*@apiParam {String {chemist, doctor}}  category Category.
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
		*            		"image": [
        *            			{
        *                			"file_id": "1",
        *                			"file_path": "http://192.168.0.167/derma_svl/uploads/doctorImages/d103d704ee77e6bc6321ed2540899579.jpg"
        *            			},
        *            			{
        *                			"file_id": "2",
        *                			"file_path": "http://192.168.0.167/derma_svl/uploads/doctorImages/884b9647211c6d0dbfc4d9c98370733c.jpg"
        *            			}
        *        			]
		*        		},
		*    		],
		*			"filterspeciality": [
        *    			{
        *        			"speciality_id": "1",
        *        			"speciality_name": "Derma",
        *        			"count": "0"
        *    			},
        *    			{
        *        			"speciality_id": "2",
        *        			"speciality_name": "CP",
        *        			"count": "0"
        *    			},
        *    			{
        *        			"speciality_id": "3",
        *        			"speciality_name": "GP",
        *        			"count": "15"
        *    			},
        *    			{
        *        			"speciality_id": "4",
        *        			"speciality_name": "Gynae",
        *        			"count": "0"
        *    			}
        *			],
		*    		"request_id": 1567847910.096092
		*		}
		*	}
		*/

		$user_id = $this->id;

		$category = $this->input_data['category'];

		if($category == 'chemist'){

			$chemistrecords = $this->model->get_records(['users_id' => $user_id], 'chemist');
			$chemistdata = [];
			$images_data = [];
			foreach($chemistrecords as $data){
				$chemist_data['id'] = $data->chemist_id;
				$chemist_data['name'] = $data->chemist_name;
				$chemist_data['address'] = $data->address;
				$state_name = $this->model->get_records(['id' => $data->state] , 'state', ['state']);
				$chemist_data['state'] = !empty($state_name[0]->state) ? $state_name[0]->state : 0;
				$city_name = $this->model->get_records(['city_id' => $data->city] , 'cities', ['city_name']);
				$chemist_data['city'] = !empty($city_name[0]->city_name) ? $city_name[0]->city_name : 0;
				$chemist_data['pincode'] = $data->pincode;

				$imageData = $this->model->get_records(['chemist_id'=>$data->chemist_id], 'images', ['image_id', 'image_name']);
				foreach($imageData as $image){
					$image_data['file_id'] = $image->image_id;
					$image_data['file_path'] = base_url($image->image_name);
					array_push($images_data, $image_data);

				}
				
				$chemist_data['image'] = $images_data;

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
				$images_data = [];
				$doctor_data['id'] = $value->doctor_id;
				$doctor_data['name'] = $value->doctor_name;

				$speciality_name = $this->model->get_records(['speciality_id'=>$value->speciality], 'speciality', ['speciality_id', 'speciality_name']);
				$doctor_data['speciality_id'] = $speciality_name[0]->speciality_id;
				$doctor_data['speciality_name'] = $speciality_name[0]->speciality_name;

				$doctor_data['address'] = $value->address;

				$state_name = $this->model->get_records(['id' => $value->state] , 'state', ['state']);
				$doctor_data['state'] = !empty($state_name[0]->state) ? $state_name[0]->state : 0;

				$city_name = $this->model->get_records(['city_id' => $value->city] , 'cities', ['city_name']);
				$doctor_data['city'] = !empty($city_name[0]->city_name) ? $city_name[0]->city_name : 0;

				$doctor_data['pincode'] = $value->pincode;

				$imageData = $this->model->get_records(['doctor_id'=>$value->doctor_id], 'images', ['image_id', 'image_name']);
				foreach($imageData as $image){
					$image_data['file_id'] = $image->image_id;
					$image_data['file_path'] = base_url($image->image_name);
					array_push($images_data, $image_data);

				}
				$doctor_data['image'] = $images_data;

				array_push($doctordata, $doctor_data);
			}

			$specialtygrid = [];
			$specialtydata = [];

			$speciality = $this->model->get_records([], 'speciality');
			if(count($speciality) > 0){
				foreach($speciality as $value){
					$speciality_data['speciality_id'] = $value->speciality_id;
					$speciality_data['speciality_name'] = $value->speciality_name;
					
					$speciality = $this->model->get_speciality_count($speciality_data['speciality_id'], $user_id);
					$speciality_data['count'] = $speciality[0]['doctor_count'];
					array_push($specialtydata, $speciality_data);
				}
				
			}

			$this->response['message'] = empty($doctorrecords) ? "No Doctor Data Found" : "Doctor List";
			$this->response['code'] = 200;
			$this->response['data'] = [
				"list" => $doctordata,
				"filterspeciality" => $specialtydata
			];
			$this->sendResponse(); // return the response
		}

		
	}

}
