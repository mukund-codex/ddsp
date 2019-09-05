<?php
class Speciality extends Api_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('api/mdl_api', 'model');
		$this->load->library('form_validation');
    }

    public function speciality_list() {
		// Speciality List To the System
		/**
		* @api {post} /api/speciality/speciality_list Speciality List
		* @apiName speciality_list
		* @apiGroup Speciality
		*
		*
		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Speciality Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Speciality List",
		*		"error": "",
		*		"data": {
		*			"specialities": [
		*				{
		*					"speciality_id": "1",
		*					"speciality_name": "Cardiologist"
		*				}
		*			],
		*			"request_id": 1561612844.983086109161376953125
		*		},
		*		"code": 200
		*	}
		*/

		$specialityRecords = $this->model->get_records([], 'speciality');
		$data = [];
		if(count($specialityRecords) > 0)  {
			foreach ($specialityRecords as $key => $value) {
				$input_data['speciality_id'] 	= $value->speciality_id;
				$input_data['speciality_name'] 	= $value->speciality_name;
				array_push($data, $input_data);
			}
		}

		$this->response['data'] = [
			"specialities" => $data
		];

		$this->response['message'] = empty($data) ? "No Speciality Found" : "Speciality List";
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
		return;
	}

}