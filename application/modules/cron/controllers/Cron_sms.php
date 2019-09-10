<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_sms extends Generic_Controller
{
	private $model_name = 'mdl_sms';

	function __construct()
	{
		parent::__construct();
		$this->load->model($this->model_name, 'model');
	}

	public function index()
	{
		$this->load->helper('send_sms');
		$doctors = $this->model->get_collection();
		// echo "<pre>";
		// print_r($doctors);exit;
		

		if(empty($doctors)) {
			return;
		}

		foreach ($doctors as $doctor) {
			$doctor_id = $doctor->doctor_id;
			$division_id = $doctor->division_id;
			$doctor_name = $doctor->name;
			$doctor_mobile = $doctor->mobile;
			$original_url = $doctor->original_url;
			$tiny_url = $doctor->tiny_url;
			$division_name = $doctor->division_name;
			$sender_id = $doctor->sender_id;

			if(empty($tiny_url)) {
				$tiny_url = $original_url;
			}

			if(empty($doctor_name) || empty($doctor_mobile) 
				|| empty($original_url) || empty($tiny_url) ) 
			{
				continue;
			}

			$sms_r = "Dear $doctor_name, ".PHP_EOL;
			$sms_r .= "We are eagerly waiting to hear from you on your experience of Daily scientific Therapy realted Messages.".PHP_EOL;
			$sms_r .= "Click here: $tiny_url ".PHP_EOL;
			$sms_r .= "To share your feedback. ".PHP_EOL;
			$sms_r .= "Wish you Happy learning! ".PHP_EOL;
			$sms_r .= "Regards, ".PHP_EOL;
			$sms_r .= "Telma AM & Telma AMH".PHP_EOL;

			send_sms($doctor_mobile, $sms_r, 'Invitation', '', '', $sender_id);
		}

		echo 'Success';
		exit;
	}

}