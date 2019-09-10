<?php
class User extends Api_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('api/mdl_user');
		$this->load->library('form_validation');

	}

	function login() {

		// User Logging in to the system
		// Get Mobile , Password and Device ID for Validating
		// If records are present, return the token to the APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/user/login User Login
		 * @apiName login
		 * @apiGroup User
		 *
		 * @apiParam {String} username Username
		 * @apiParam {String} password Password.
		 * @apiParam {String}  device_id Device ID.
		 * @apiParam {String {android, ios}}  device_type Device Type.
		 * @apiParam {String}  os Operating System.
		 * @apiParam {String}  device_name Device Name.
		 * @apiParam {String}  app_version APP version.
		 *
		 * @apiSuccess {Number} code HTTP Status Code.
		 * @apiSuccess {String} message  Associated Message.
		 * @apiSuccess {Object} data  Employee Record Object With Token
		 * @apiSuccess {Object} error  Error if Any.
		 *
		 * @apiSuccessExample Success-Response:
			*     HTTP/1.1 200 OK
			*     {
			*		"message": "Login Successful",
			*		"error": "",
			*		"code": 200,
			*		"data": {
			*			"token": "26eb54a11be4b1a770cdf83de3249d5d5a48b2c8",
			*			"name": "test asm",
			*			"hq": "area 1",
			*			"mobile_number": "1245788888",
			*			"email": null,
			*			"android_version": "1",
			*			"request_id": 1562048058.649876117706298828125
			*		}
			*	}
			*/

		$username = trim(isset($this->input_data['username'])?$this->input_data['username']:'');
		$password = trim(isset($this->input_data['password'])?$this->input_data['password']:'');
		$device_id = trim(isset($this->input_data['device_id'])?$this->input_data['device_id']:'');
		$device_type = trim(isset($this->input_data['device_type'])?$this->input_data['device_type']:'');
		$os = trim(isset($this->input_data['os'])?$this->input_data['os']:'');
		$device_name = trim(isset($this->input_data['device_name'])?$this->input_data['device_name']:'');
		$app_version = trim(isset($this->input_data['app_version'])?$this->input_data['app_version']:'');
		
		if(empty($username)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Username is Mandatory";
			$this->error = array('message'=>'Username Id Mandatory!');
			$this->sendResponse();
			return;
		}

		if(empty($password)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Password is Mandatory";
			$this->error = array('message'=>'Password is Mandatory!');
			$this->sendResponse();
			return;
		}

		if(empty($device_id)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Request";
			$this->error = array('message'=>'Invalid Request');
			$this->sendResponse();
			return;
		}

		if(empty($device_type) || !in_array($device_type,['android','ios'])) {
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Request";
			$this->error = array('message'=>'Invalid Request');
			$this->sendResponse();
			return;
		}

		if(empty($os)){
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Request";
			$this->error = array('message'=>'Invalid Request');
			$this->sendResponse();
			return;
		}

		if(empty($device_name)){
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Request";
			$this->error = array('message'=>'Invalid Request');
			$this->sendResponse();
			return;
		}

		if(empty($app_version)){
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Request";
			$this->error = array('message'=>'Invalid Request');
			$this->sendResponse();
			return;
		}

		// $user = $this->mdl_user->getUserRecords(['users_emp_id' => $username, 'users_password' => $password], 'manpower',['users_id','users_name','users_mobile']);
		$user = $this->mdl_user->getUserRecords($username, $password);

		if(!count($user)) {
			$this->response['code'] = 400;
			$this->response['message'] = "Incorrect Username or Password";
			$this->error = array('message'=>'Incorrect Username or Password!');
			$this->sendResponse();
			return;
		} else {
			if(!empty($user)){
				$users_id = $user[0]->userId;
				$name = $user[0]->users_name;
				$type = $user[0]->users_type;
				$mobile_number = $user[0]->users_mobile;
				$email = $user[0]->users_email;
				$reporting_manager = $user[0]->reporting_manager;

				if($type == 'MR') {
					$hq = $user[0]->city_name;
				} else if($type == 'ASM') {
					$hq = $user[0]->area_name;
				}

				//Set access_token
				$token = sha1(md5(microtime() . "" . $users_id));
				$access_data = array();
				$access_data['user_id'] 			= $users_id;
				$access_data['access_token'] 		= $token;
				$access_data['device_id'] 			= $device_id;
				$access_data['device_type'] 		= $device_type;
				$access_data['os'] 			    	= $os;
				$access_data['device_name']	    	= $device_name;
				$access_data['app_version'] 		= $app_version;

				$access_granted_token = $this->mdl_user->_insert($access_data,'access_token'); // User is logged in, Generate the Access Token

				$response_token = $this->mdl_user->get_records(['user_id' => $users_id ],'access_token',['access_token'],'');
				$android_version = $this->mdl_user->get_records(['os'=>'android'],'version_control',['version_code','store_version_code','version_status'],'','1');
				$ios_version = $this->mdl_user->get_records(['os'=>'ios'],'version_control',['version_code','store_version_code','version_status'],'','1');

				$data = array();
				$data['token'] = $token;				
				$data['name'] = $name;
				$data['hq'] = $hq;
				$data['mobile_number'] = $mobile_number;
				$data['email'] = $email;
				$data['reporting_manager'] = $reporting_manager;
				$data['android_version'] = (int)isset($android_verion[0]->version_code) ? $android_verion[0]->version_code : '0';
				$data['ios_version'] = (int)isset($ios_version[0]->version_code) ? $ios_version[0]->version_code : '0';

				$this->response['code'] = 200;
				$this->response['message'] = "Login Successful";
				$this->response['data'] = $data;
				$this->sendResponse();
			}else{
				$this->response['code'] = 400;
				$this->response['message'] = "Login Failure";
				$this->error = array('message'=>'Something went Wrong! Please Try Again.');
				$this->sendResponse();
			}
		}
	}

	function logout() {

		// Get User Id from APP
		// If User Token id is valid, update the token_status as inactive to the APP
		// Else return the error message to the APP

		/**
		 * @api {post} /api/user/logout User Logout
		 * @apiName logout
		 * @apiGroup User
		 *
		 * @apiHeader {String} Token User unique Access-Token
		 *
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *	    	{
		 *			    "code": 200,
		 *		    	"message": "Logout Successful",
		 *				 	"data":
		 *					{
		 *						"request_id": 1520588899.0994
		 *				 	},
		 *				 	"error": "",
		 *				 	"latest_version":
		 *				 	{
		 *						 "android": "6",
		 *						 "ios": "1.8"
		 *				 	}
		 *  		 	}
		 *  	 }
		 */

		$access_token = $this->accesstoken;

		$data['token_status'] = 'inactive';

		if($this->mdl_user->_update(['access_token'=>$access_token],$data,'access_token')){
			$this->response['code'] = 200;
			$this->response['message'] = "Logout Successful";
			$this->error = array('message'=>'Logout Successful');
			$this->sendResponse();
			return;
		}else{
			$this->response['code'] = 400;
			$this->response['message'] = "Logout Failure. Please Try Again";
			$this->error = array('message'=>'Logout Failure. Please Try Again');
			$this->sendResponse();
			return;
		}
	}

	function forgot_password(){

		// Get User Employee Id from APP
		// If User Employee Id is valid, Send SMS to user's mobile number
		// Else return the error message to the APP

		/**
		 * @api {post} /api/user/forget_password User forget password
		 * @apiName forget_password
		 * @apiGroup User
		 *
		 * @apiParam {String} users_emp_id users_emp_id
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *	    	{
		 *				"message": "SMS Sent",
		 *				"error": "",
		 *				"code": 200,
		 *				"data": {
		 * 					"request_id": 1567491126.243076
		 *				}
		 *			}
		 *  	}
		 */
		
		$users_emp_id = (int) trim(isset($this->input_data['users_emp_id'])?$this->input_data['users_emp_id']:'');

		if(empty($users_emp_id)){
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Employee ID. Please Try Again.";
			$this->error = array('message' => 'Invalid Employee ID, Please Try Again');
			$this->sendResponse();
			return;
		}

		$get_user = $this->mdl_user->get_records(['users_emp_id' => $users_emp_id, 'users_type' => 'MR'],'manpower');

		if(!$get_user){
			$this->response['code'] = 400;
			$this->response['message'] = "Invalid Employee Id. Please Try Again";
			$this->error = array('message'=>'Invalid Employee Id. Please Try Again');
			$this->sendResponse();
			return;
		}

		$this->load->helper(['send_sms', 'bitly_url']);
		$this->load->library('common');

		$users_id = $get_user[0]->users_id;
		$users_mobile = $get_user[0]->users_mobile;
		$users_name = $get_user[0]->users_name;
		
		$request_token = $this->common->generate_random_string();
		$r_token = base64_encode($request_token);
		
		$url = base_url("login/user/forgot_password?rid=$r_token");

		$short_url = bitly_url($url);

		$msg = "Dear $users_name, ".PHP_EOL."Below is the link to change your password.".PHP_EQL."$short_url";
		
		$msg_for = "Forgot Password";

		$request_data['users_id'] = $users_id;
		$request_data['request_token'] = $request_token;
		$request_data['url'] = $url;
		$request_data['short_url'] = $short_url;
		$request_data['status'] = 1;

		$request_id = $this->mdl_user->_insert($request_data, 'forgot_password_request');

		if($request_id){
			$this->response['code'] = 200;
			$this->response['message'] = "SMS Sent";
			$this->sendResponse();
			return;	
		}
		
		$this->response['code'] = 400;
		$this->response['message'] = "Something went wrong, Please try again.";
		$this->sendResponse();
		return;	
		
	}

	function feedback(){

		// Add User's Feedback To the System
		/**
		* @api {post} /api/user/feedback Add Feedback
		* @apiName feedback
		* @apiGroup User
		*

		*
		* @apiParam {Number} rating rating
		* @apiParam {String} message message

		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Feedback Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
    	*			"message": "Feedback submitted successfully.",
   		*			"error": "",
    	*			"code": 200,
    	*			"data": {
        *				"request_id": 1567494992.2654
    	*			}
		*		}
		*/
		$user_id = $this->id;

		$rating = (int)trim(isset($this->input_data['rating'])?$this->input_data['rating']:'');
		$message = trim(isset($this->input_data['message'])?$this->input_data['message']:'');
		
		if(empty($rating) || empty($message)){
			$this->response['code'] = 401;
			$this->response['message'] = "Please give complete feedback.";
			$this->error = array('message' => 'Please give complete feedback.');
			$this->sendResponse();
			return;
		}
		
		$feedbackData = [];
		$feedbackData['users_id'] = $user_id;
		$feedbackData['rating'] = $rating;
		$feedbackData['message'] = $message;

		$feedback_id = $this->mdl_user->_insert($feedbackData, 'users_feedback');

		if(!$feedback_id){
			$this->response['code'] = 500;
			$this->response['message'] = "Internal Server Error.";
			$this->error = array('message' => 'Internal Server Error.');
			$this->sendResponse();
			return;
		}
		
		$this->response['code'] = 200;
		$this->response['message'] = "Feedback submitted successfully.";
		//$this->error = array('message' => 'Feedback submitted successfully.');
		$this->sendResponse();
		return;

	}

	function troubleshoot(){

		// Add User's Troubleshoot To the System
		/**
		* @api {post} /api/user/troubleshoot Add Troubleshoot
		* @apiName troubleshoot
		* @apiGroup User
		*

		*
		* @apiParam {String} message message
		* @apiParam {file} photo image

		* @apiSuccess {Number} code HTTP Status Code.
		* @apiSuccess {String} message  Associated Message.
		* @apiSuccess {Object} data  Troubleshoot Record Object With Token
		* @apiSuccess {Object} error  Error if Any.
		*
		* @apiSuccessExample Success-Response:
		*     HTTP/1.1 200 OK
		*     {
		*		"message": "Troubleshoot submitted successfully.",
		*		"error": "",
		*		"code": 200,
		*		"data": {
		*			"request_id": 1561630916.4044220447540283203125
		*		}
		*	}
		*/
		$user_id = $this->id;
		
		$message = $this->input->post('message');

		if(empty($message)){
			$this->response['code'] = 400;
			$this->response['message'] = "Please enter message.";
			$this->error = array('message'=> 'Please enter message.');
			$this->sendResponse();
			return;
		}

		$troubleshootData = [];

		if(!empty($_FILES['photo'])) {
			
			$this->load->helper('upload_media');
			$is_file_upload = upload_media('photo', 'uploads/troubleshoot', ['jpeg', 'png', 'jpg'], 10000000);
	
			if(array_key_exists('error', $is_file_upload)) {
				$this->response['code'] = 400;
				$this->response['message'] = $is_file_upload['error'];
				$this->error = array('message'=> $is_file_upload['error']);
				$this->sendResponse();
				return;
			}
			
			if(!empty($is_file_upload)) {
				$troubleshootData['image'] = $is_file_upload[0]['file_name'];
			}
		}		

		$troubleshootData['users_id'] = $user_id;
		$troubleshootData['message'] = $message;

		$troubleshoot_id = $this->mdl_user->_insert($troubleshootData, 'troubleshoot');

		if(!$troubleshoot_id){
			$this->response['code'] = 500;
			$this->response['message'] = "Internal Server Error.";
			$this->error = array('message' => 'Internal Server Error.');
			$this->sendResponse();
			return;
		}
		
		$this->response['code'] = 200;
		$this->response['message'] = "Troubleshoot submitted successfully.";
		$this->sendResponse();
		return;		
	}
}
