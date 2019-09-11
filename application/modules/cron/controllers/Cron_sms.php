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
		$data = $this->model->get_collection();		

		if(empty($data)) {
			return;
		}
		
		$mr_msg = '';
		$asm_details = [];

		foreach($data as $details){
			$asm = [];
			$total_chemist = $details->total_chemist;
			$total_doctor = $details->total_doctor;
			$users_id = $details->users_id;
			$users_name = $details->users_name;

			$asm['zsm_name'] = $details->zsm_name;
			$asm['zsm_mobile'] = $details->zsm_mobile;
			$asm['name'] = $details->asm_name;
			$asm['count'] = $total_chemist;

			$mr_msg .= "$users_name - $total_chemist, ";
			array_push($asm_details, $asm);
		}
		
		echo "<pre>";
		print_r($asm_details);echo "<br>";
	
		$asmmsg = '';

		foreach($asm_details as $details){
			$asm_name = $details['name'];
			$asm_count = $details['count'];
			$asm_msg = "$asm_name - $asm_count";
			$asmmsg .= "$asm_msg, ";
			
		}
		$asmmsg = rtrim($asmmsg, ", ");

		$sms_r = "Yesterday’s chemist met count of your team ABM wise- $asmmsg";
		echo $sms_r;exit;

		//send_sms($doctor_mobile, $sms_r, 'Invitation', '', '', $sender_id);

		echo 'Success';
		exit;
	}

}