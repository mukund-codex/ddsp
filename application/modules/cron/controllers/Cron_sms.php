<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_sms extends Generic_Controller
{
	private $model_name = 'mdl_sms';

	function __construct()
	{
		parent::__construct();
		$this->load->model($this->model_name, 'model');
		$this->load->helper('send_sms');
	}

	public function asm_wise_count()
	{	
		$yesterday = date('Y/m/d',strtotime("-1 days"));

		$data = $this->model->get_asm_data($yesterday);		

		if(empty($data)) {
			echo "No Records";
			return;
		}
		
		foreach($data as $details){
			$asm_msg = '';
			$zsm_name = $details->zsm_name;
			$zsm_mobile = $details->zsm_mobile;
			$log = $details->log;

			if(empty($zsm_name)){ continue; }
			if(empty($zsm_mobile)){ continue; }
			if(empty($log)){ continue; }

			$asm_msg = "Yesterday’s chemist met count of your team ABM wise- ".$log.".";

			send_sms($zsm_mobile, $asm_msg, 'Chemist Count Reminder');

		}

		echo 'Success';
		exit;
		
	}

	function mr_wise_count(){

		$yesterday = date('Y-m-d',strtotime("-1 days"));

		$mrdata = $this->model->get_mr_data($yesterday);

		if(empty($mrdata)){
			echo "No Records";
			return;
		}

		foreach($mrdata as $details){
			$mr_msg = '';
			$asm_name = $details->asm_name;
			$asm_mobile = $details->asm_mobile;
			$log = $details->log;

			if(empty($asm_name)){ continue; }
			if(empty($asm_mobile)){ continue; }
			if(empty($log)){ continue; }

			$mr_msg = "Yesterday’s chemist met count of your team- ".$log.".";

			send_sms($asm_mobile, $mr_msg, 'Chemist Count Reminder');

		}
		echo "Success";
		exit;

	}

}