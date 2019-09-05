<?php
class MY_Exceptions extends CI_Exceptions{

	public $response = array('message'=> 'ERROR', 'error'=>"");
	protected $is_api = false;
	private $CI;

	function __construct(){
		// echo 'fdsafdsaf';die();
		parent::__construct();

		$this->CI = & get_instance();
		$segments = $this->CI->uri->segment_array();
		if(in_array('api', $segments)) {
			$this->is_api = true;
		}
	}

	/**
	 * General Error Page
	 *
	 * Takes an error message as input (either as a string or an array)
	 * and displays it using the specified template.
	 *
	 * @param	string		$heading	Page heading
	 * @param	string|string[]	$message	Error message
	 * @param	string		$template	Template name
	 * @param 	int		$status_code	(default: 500)
	 *
	 * @return	string	Error page output
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500) {
		if($this->is_api){
			$this->format_response($status_code,'failure', $message, '');
		}else {
			parent::show_error($heading, $message, $template, $status_code);
		}
	}

	// --------------------------------------------------------------------

	public function show_exception($exception) {
		if($this->is_api){
			$message = $exception->getMessage();
			$this->format_response(500,'failure', $message, '');
		}else {
			parent::show_exception($exception);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Native PHP error handler
	 *
	 * @param	int	$severity	Error level
	 * @param	string	$message	Error message
	 * @param	string	$filepath	File path
	 * @param	int	$line		Line number
	 * @return	void
	 */
	public function show_php_error($severity, $message, $filepath, $line) {
		if($this->is_api){
			$this->format_response(500,'failure', $message, '');
		}else {
			parent::show_exception($severity, $message, $filepath, $line);
		}
	}

	protected function format_response($code = 500, $response = 'failure', $msg = '', $err = ''){
		$err_response = [];
		$err_response['code'] = $code;
		$err_response['response'] = $response;
		$err_response['message'] = $msg;
		$err_response['error'] = $err;

		$this->CI->output
			->set_status_header($code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($err_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
			->_display();
		exit;
	}

}
