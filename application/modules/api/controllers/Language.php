<?php
class Language extends Api_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('api/mdl_api', 'model');
		$this->load->library('form_validation');
    }

    public function language_list() {
		// Language List To the System
		/**
		* @api {post} /api/language/language_list Language List
		* @apiName language_list
		* @apiGroup Language
		*
		*
		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Language Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Language List",
		*		"error": "",
		*		"data": {
		*			"languages": [
		*				{
		*					"language_id": "1",
		*					"language_name": "English"
		*				}
		*			],
		*			"request_id": 1561612844.983086109161376953125
		*		},
		*		"code": 200
		*	}
		*/

		$languageRecords = $this->model->get_records([], 'language');
		$data = [];
		if(count($languageRecords) > 0)  {
			foreach ($languageRecords as $key => $value) {
				$input_data['language_id'] 	= $value->language_id;
				$input_data['language_name'] 	= $value->language_name;
				array_push($data, $input_data);
			}
		}

		$this->response['data'] = [
			"languages" => $data
		];

		$this->response['message'] = empty($data) ? "No Language Found" : "Language List";
		$this->response['code'] = 200;
		$this->sendResponse(); // return the response
		return;
	}

}