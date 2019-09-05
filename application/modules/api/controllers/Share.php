<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Share extends MX_Controller
{
	public $data = [];
	public $module = 'share';

	function __construct() {
		parent::__construct();
		$this->load->model('mdl_share');
		$this->load->config('s3');
		$this->load->library('s3');
	}

	function index($doctor_id, $whatsapp){
		if(!$doctor_id){
			echo 'Invalid';
			exit;
		}else{
			$response = $this->mdl_share->get_records(['doctor_id' => $doctor_id], 'doctor_poster', ['poster', 's3_url']);
			if(! $response) {
				echo 'Invalid';
				exit;
			}

			$doctor_result = $this->mdl_share->get_records(['doctor_id' => $doctor_id], 'doctor');
			if(! $doctor_result) {
				echo 'Invalid';
				exit;
			}

			$status = ($whatsapp) ? 'share' : 'download';
			$file_path = $response[0]->s3_url;
			// echo $file_path;exit;
			$update_video_status = $this->mdl_share->_insert([
				'doctor_id' => $doctor_id,
				'type' => $status, 
				'file_path' => $file_path
			], 'image_status');

			$quoted = sprintf('"%s"', addcslashes(basename($file_path), '"\\'));
			$s3_base_url = $this->config->item('s3_base_url');
			$bucket = $this->config->item('s3_bucket_name');
			$result = S3::getObject($bucket, $file_path);
			if($status == 'share') {
				header('Content-Description: File Transfer');
				header('Content-Type: image/jpg');
				header("Content-Disposition: inline");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				echo $result->body;
				exit;
			} else {
				header('Content-Description: File Transfer');
				header('Content-Type: image/jpg');
				header("Content-Disposition: attachment; filename=abc.jpg");
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				echo $result->body;
				exit;
			}

			
		}
	}
}
