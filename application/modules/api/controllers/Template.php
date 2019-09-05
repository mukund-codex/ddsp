<?php
class Template extends Api_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('api/mdl_template', 'model');
		$this->load->library('form_validation');
		$this->load->library('s3');
		$this->load->config('s3');
		$this->s3_base_url = $this->config->item('s3_base_url');
	}

	function template_list()
	{
		// Template List To the System
		/**
		* @api {post} /api/template/template_list Template List
		* @apiName template_list
		* @apiGroup Template
		*
		*
		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Template Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*       {
        *            "message": "Template List",
        *            "error": "",
        *            "data": {
        *                "templates": [
        *                    {
        *                        "template_id": "1",
        *                        "template_name": "testfff",
        *                        "template_image": "http://localhost/web-poster/uploads/templates/d20a3c5321202939dcc4d0717a3c2404.png"
        *                    },
        *                    {
        *                        "template_id": "2",
        *                        "template_name": "My New Template",
        *                        "template_image": "http://localhost/web-poster/uploads/templates/8be6ae5d2a73a6039ee5dbcb7b54afdd.png"
        *                    }
        *                ],
        *                "request_id": 1561612370.0373060703277587890625
        *            },
        *            "code": 200
        *        }
		*/

		$template_list = $this->model->get_records([], 'template');
		$version_data = $this->model->get_records([], 'version_control');
		$data = [];
		if(count($template_list) > 0)  {
			foreach ($template_list as $key => $value) {
				$input_data['template_id'] 		= $value->template_id;
				$input_data['template_name'] 	= $value->template_name;
				$input_data['template_image'] 	= base_url().$value->dummy_template;
				array_push($data, $input_data);
			}
		}

		$this->response['data'] = [
			"templates" 	=> $data,
			"version_code"	=> $version_data[0]->version_code
		];

		$this->response['message'] = empty($data) ? "No Templates Found" : "Template List";
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
		return;		
	}

	function generate_templates() {

		// Generate Templates of Doctor
		/**
		* @api {post} /api/template/generate_templates Template Preview List
		* @apiName generate_templates
		* @apiGroup Template
		*
		* @apiParam {Number} doctor_id Doctor ID

		*
		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Template Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*       {
		*			"message": "Posters Generated Successfully!",
		*			"error": "",
		*			"data": {
		*				"preview_templates": [
		*					{
		*						"template_id": "1",
		*						"poster": "http://localhost/zydus-doctor-poster/uploads/previews/test-doctor-29164.jpg"
		*					},
		*					{
		*						"template_id": "2",
		*						"poster": "http://localhost/zydus-doctor-poster/uploads/previews/test-doctor-28491.jpg"
		*					}
		*				],
		*				"request_id": 1562224665.3947250843048095703125
		*			},
		*			"code": 200
		*		}
		*/

		$doctor_id = $this->input_data['doctor_id'];
		$doctorData = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor');

		if(empty($doctorData)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor is Mandatory";
			$this->error = array('message'=>'Doctor Mandatory!');
			$this->sendResponse();
			return;
		}


		$doctor_speciality = $doctorData[0]->speciality_id;

		$specialityData = $this->model->get_records(['speciality_id' => $doctor_speciality], 'speciality');
		$doctor_speciality = $specialityData[0]->speciality_name;

		$templates = $this->model->get_records([], 'template');
		$data['doctor'] = $doctorData[0]->doctor_name;
		$data['clinic_address'] = $doctorData[0]->clinic_address;
		$data['doctor_speciality'] = $doctor_speciality;
		$doctor_photo = $doctorData[0]->photo;
		foreach($templates as $template) {
			$poster_name = $this->model->generate($data, $doctor_photo, $template->name_size, $template->message_size, $template->thumb_size, $template->thumb_x_location, $template->thumb_y_location, $template->name_y_location, $template->message_y_location, $template->template_image,$template->name_x_location,$template->name_height,$template->name_width,$template->speciality_size,$template->speciality_x_location,$template->speciality_y_location,$template->speciality_width,$template->speciality_height,$template->address_size,$template->address_x_location,$template->address_y_location,$template->address_width,$template->address_height,  'uploads/previews');
			$temp = explode('/',$poster_name);
			$s3_url = $this->s3_base_url."previews/".$temp[2];
			$insertData = ['doctor_id' => $doctor_id, 'poster' => $poster_name, 'template_id' => $template->template_id, 's3_url' => $s3_url];
			$doctor_poster_id = $this->model->_insert($insertData, 'doctor_template_previews');
			$responseData['preview_templates'][] = ['template_id' => $template->template_id, 'poster' => $s3_url];
		}

		$this->response['message'] = "Previews Generated Successfully!";
		$this->response['data'] = $responseData;
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
		return;
	}

	function generate_poster() {

		// Generate Poster To the System
		/**
		* @api {post} /api/template/generate_poster Generate Poster
		* @apiName generate_poster
		* @apiGroup Template
		*
		*
		* @apiParam {Number} doctor_id Doctor ID
		* @apiParam {Number} template_id Template ID

		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Template Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*       {
		*			"message": "Records Added Successfully!",
		*			"error": "",
		*			"code": 200,
		*			"data": {
		*				"request_id": 1561715078.7814919948577880859375
		*			}
		*		}
		*/


		$template_id = $this->input_data['template_id'];
		$doctor_id = $this->input_data['doctor_id'];

		$doctorData = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor');
		if(empty($doctorData)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor is Mandatory";
			$this->error = array('message'=>'Doctor Mandatory!');
			$this->sendResponse();
			return;
		}

		$url = getcwd().'/'.$doctorData[0]->photo;
		if(!file_exists($url) || empty($doctorData[0]->photo) ) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor photo does not exist!";
			$this->error = array('message'=>'Doctor photo does not exist!');
			$this->sendResponse();
			return;
		}

		$templateData = $this->model->get_records(['template_id' => $template_id], 'template');
		if(empty($templateData)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Template is Mandatory";
			$this->error = array('message'=>'Template Mandatory!');
			$this->sendResponse();
			return;
		}

		$doctorPosterData = $this->model->get_records(['doctor_id' => $doctor_id], 'doctor_poster');
		if(!empty($doctorPosterData)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Doctor Poster already generated!";
			$this->error = array('message'=>'Doctor Poster already generated!');
			$this->sendResponse();
			return;
		}

		$doctor_name = $doctorData[0]->doctor_name;
		$doctor_id = $doctorData[0]->doctor_id;
		$doctor_photo = $doctorData[0]->photo;
		$clinic_address = $doctorData[0]->clinic_address;
		$doctor_speciality = $doctorData[0]->speciality_id;

		$specialityData = $this->model->get_records(['speciality_id' => $doctor_speciality], 'speciality');
		$doctor_speciality = $specialityData[0]->speciality_name;

		$data['doctor'] = $doctor_name;
		$data['doctor_speciality'] = $doctor_speciality;
		$data['clinic_address'] = $clinic_address;

		$poster_name = $this->model->generate($data, $doctor_photo, $templateData[0]->name_size, $templateData[0]->message_size, $templateData[0]->thumb_size, $templateData[0]->thumb_x_location, $templateData[0]->thumb_y_location, $templateData[0]->name_y_location, $templateData[0]->message_y_location, $templateData[0]->template_image,$templateData[0]->name_x_location,$templateData[0]->name_height,$templateData[0]->name_width,$templateData[0]->speciality_size,$templateData[0]->speciality_x_location,$templateData[0]->speciality_y_location,$templateData[0]->speciality_width,$templateData[0]->speciality_height,$templateData[0]->address_size,$templateData[0]->address_x_location,$templateData[0]->address_y_location,$templateData[0]->address_width,$templateData[0]->address_height,  'uploads/posters');
		$temp = explode('/',$poster_name);
		$s3_url = $this->s3_base_url."posters/".$temp[2];
		$insertData = ['doctor_id' => $doctor_id, 'poster' => $poster_name, 's3_url' => "posters/".$temp[2]];
		$doctor_poster_id = $this->model->_insert($insertData, 'doctor_poster');
		$responseData = ['url' => base_url().$poster_name];

		$this->response['message'] = !($doctor_poster_id) ? "Internal Server Error" : "Records Added Successfully!";
		$this->response['code'] = 200;
		$this->response['data'] = $responseData;
		$this->sendResponse(); // return the response
		return;
	}
}
