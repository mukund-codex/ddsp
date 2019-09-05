<?php
class Api_Controller extends MX_Controller{

	public $response = array('message'=> 'OK', 'error'=>"");
	protected $id = 0;
	protected $device_id = NULL;
	protected $device_type = NULL;
	protected $app_version = NULL;
	protected $error = array();
	protected $input_data = array();
	protected $latest_version = array();
	protected $accesstoken = NULL;

	private $log_file = null;
	private $request_id = null;

	function __construct(){
		ini_set('memory_limit', '-1');
		parent::__construct();

		$this->log_file= APPPATH . 'logs/apilog' . date('Y-m-d') . ".log";
		$this->request_id = microtime(true);

		$this->load->model('api/mdl_api');
		$this->load->library('form_validation');

		$segment = $this->uri->segment(3);

		if($this->input->input_stream('data') && false && !in_array($segment, ['saveallmultipart']) ){
			$input = rawurldecode($this->input->input_stream('data'));
			$input = json_decode($input, TRUE);
			//$this->input_data = arrayMapRecursive('trim', $input);

			$this->input_data = $input;
		}
		elseif( $segment == 'saveallmultipart'){
			$form_data['post_data'] = $_POST;
			$form_data['files'] = $_FILES;

			$this->log($form_data);
		}
		else {

			$input = rawurldecode(trim(file_get_contents('php://input')));
			$this->log($input);

			if($input){
				$input = json_decode($input, TRUE);

				if(!json_last_error()) {
					//$this->input_data = arrayMapRecursive('trim', $input);
					$this->input_data = $input ;

				} else {
					$this->response['code'] = 200;
					$this->response['message'] = "Bad Request. JSON ERROR";

					$this->log("BAD JSON REQUEST");

					$this->output->set_status_header(200)->set_content_type('application/json');
					header("Content-Type: application/json");
					echo json_encode($this->response);
					exit;
				}
			}
		}

		if( !in_array($segment,['login','register','forgot_password', 'get_masters'])){
			$this->authRequired($segment);
		}
	}

	protected function authRequired($segment){

		$data = $this->input->request_headers(TRUE);
		//print_r($data); die();
		//$data = $this->input_data;

		#$accesstoken = isset($data['Access-Token']) ? trim(str_replace("bearer", "", $data['Access-Token'])) : false;
		$accesstoken = isset($data['Token']) ? $data['Token'] : false; 
		
		$valid_token = false;
		$this->log("REQUEST STARTED FOR TOKEN :: " . $accesstoken);

		if($accesstoken){
			$token_record = $this->mdl_api->validateToken($accesstoken);

			if( count($token_record) ){
				$valid_token = TRUE;
	 			$this->id = $token_record[0]->user_id;
				$this->accesstoken = $accesstoken;

				$this->device_id = $token_record[0]->device_id;
				$this->device_type = $token_record[0]->device_type;
				$this->app_version = $token_record[0]->app_version;

			}

			/*
			if($valid_token['app_version'] < $this->latest_version[$this->device_type] && $segment != 'update_version'){
				$this->response['message'] = "Update Required";
				$this->response['code'] = 200;
				$this->output->set_status_header(200)->set_content_type('application/json');
				header("Content-Type: application/json");
				echo json_encode($this->response);
				exit;
			}*/
		}

		if( !$accesstoken || !$valid_token ) {
			$this->response['message'] = "Authentication Required";
			$this->response['code'] = 401;

			$this->log("AUTHENTICATION FAILED FOR TOKEN :: " . $accesstoken);

			$this->output->set_status_header(401)->set_content_type('application/json');
			header("Content-Type: application/json");
			echo json_encode($this->response);
			exit;
		}
	}

	protected function sendResponse(){
		$this->response['data']['request_id'] = $this->request_id;
		$this->log($this->response);
		/* Only One Error Message */
		if(!empty($this->error)){
			$this->response['error'] = array_pop($this->error);
		}

		$this->response['message'] =$this->response['message'];
		$response = json_encode($this->response);

		$this->log("REQUEST ENDED FOR TOKEN :: " . $this->accesstoken);

		$this->output->set_status_header($this->response['code'])->set_content_type('application/json')->set_output($response);
	}

	protected function log($message){
		if(is_array($message)){
			$message = date('Y-m-d h:i:s') . " Token :: " . $this->accesstoken . "  :: RequestId :: " . $this->request_id . " :: Message :: " . json_encode($message) . PHP_EOL;
		} else {
			$message = date('Y-m-d h:i:s') . " Token :: " . $this->accesstoken . " :: RequestId :: " . $this->request_id . " :: Message :: " . $message . PHP_EOL;
		}

		error_log($message, 3, $this->log_file);
	}

	protected function format_response($code = 500, $response = 'failure', $msg = '', $err = ''){
		$this->response['code'] = $code;
		$this->response['response'] = $response;
		$this->response['message'] = $msg;
		$this->response['error'] = $err;
	}

}
